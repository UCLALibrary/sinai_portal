<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Laravel\Scout\Searchable;
use App\Traits\HasRelatedEntities;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TextUnit extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'ark',
        'label',
        'json',
    ];
    
    public function getFillableFields($data, $json)
    {
        return array_combine($this->fillable, [
            basename($data['ark']),  // use the trailing ark segment as the id
            $data['ark'],
            $data['label'],
            $json,
        ]);
    }
    
    public static $config = [
        'index' => [
            'columns' => [
                'id' => 'Id',
                'ark' => 'Ark',
                'label' => 'Label',
            ],
        ],
    ];

    /**
     * Returns the "source" for the text unit, which consists of the state label of its parent 
     * layer and the shelfmark of its parent layer's manuscript.
     * 
     * @return array
     */
    public function getSource(): array
    {
        $query = "
            SELECT
                l.jsonb->'state'->>'label' AS state_label,
                m.jsonb->>'shelfmark' AS shelfmark
            FROM text_units tu
            JOIN LATERAL
                jsonb_array_elements_text(tu.jsonb->'parent') AS tu_parent(ark)
                ON TRUE
            JOIN layers l ON l.jsonb->>'ark' = tu_parent.ark
            JOIN LATERAL
                jsonb_array_elements_text(l.jsonb->'parent') AS l_parent(ark)
                ON TRUE
            JOIN manuscripts m ON m.jsonb->>'ark' = l_parent.ark
            WHERE m.jsonb->'type'->>'id' = 'manuscript'
            AND tu.jsonb->>'ark' = :text_unit_ark;
        ";
        
        $bindings = [
            'text_unit_ark' => $this->ark,
        ];

        $rows = DB::select($query, $bindings);
        $results = array_map(function($item) {
            return (array) $item;
        }, $rows);

        return $results;
    }

    /**
     * Accessor to include related agents when the model is serialized.
     *
     * @return array
     */
    public function getRelatedAgentsAttribute(): array
    {
        $relatedCreators = $this->getConnectedAgentCreatorNames();
        $relatedAgents = $this->getRelatedEntities(
            'assoc_name',
            Agent::class,
            null,
            function ($agent, $item) {
                return [
                    'id' => $agent->id,
                    'as_written' => $item['as_written'] ?? null,
                    'pref_name' => $agent->pref_name,
                    'rel' => $item['rel'] ?? null,
                    'role' => $item['role'] ?? null,
                    'note' => $item['note'] ?? [],
                ];
            })->toArray();
        
        return array_merge($relatedCreators, $relatedAgents);
    }

    public function getConnectedAgentCreatorNames()
    {
        $query = "
            WITH text_unit_works AS (
                SELECT DISTINCT jsonb_array_elements(tu.jsonb -> 'work_wit') -> 'work' ->> 'id' AS work_ark
                FROM text_units AS tu
                WHERE tu.jsonb ->> 'ark' = ?
            ),
            work_agents AS (
                SELECT DISTINCT
                    creator_elem ->> 'id' AS agent_ark,
                    creator_elem -> 'role' ->> 'id' AS role_id,
                    creator_elem -> 'role' ->> 'label' AS role_label
                FROM works AS work
                JOIN text_unit_works ON work.jsonb ->> 'ark' = text_unit_works.work_ark
                JOIN LATERAL jsonb_array_elements(work.jsonb -> 'creator') AS creator_elem ON TRUE
            )
            SELECT DISTINCT agent.id, agent.jsonb ->> 'pref_name' AS pref_name,
                work_agents.role_id, work_agents.role_label
            FROM agents AS agent
            JOIN work_agents ON agent.jsonb ->> 'ark' = work_agents.agent_ark;
        ";

        $bindings = [
            $this->ark,
        ];

        $results = DB::select($query, $bindings);

        return array_map(function ($row) {
            return [
                'id' => $row->id,
                'pref_name' => $row->pref_name,
                'role' => [
                    'id' => $row->role_id ?? null,
                    'label' => $row->role_label ?? null,
                ],
            ];
        }, $results);
    }

    /**
     * Get the parent layers.
     *
     * @return string|null
     */
    public function getParentLayers()
    {
        $data = $this->getJsonData();

        return !empty($data['parent'])
            ? Layer::whereIn('ark', $data['parent'])->get()
            : null;
    }

    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $array['ark'] = $this->ark ?? null;
        $array['label'] = $this->label ?? null;

        // min and max date from the 'assoc_date' field from layers of type 'origin'
        foreach($this->getParentLayers() as $layer) {
            $data = $layer->getJsonData();

            if (isset($data['assoc_date'])) {
                $notBeforeValues = [];
                $notAfterValues = [];
                foreach($data['assoc_date'] as $date) {
                    if (isset($date['type']['id']) && $date['type']['id'] === 'origin') {
                        $notBeforeValues[] = $date['iso']['not_before'] ?? null;
                        $notAfterValues[] = $date['iso']['not_after'] ?? null;
                    }
                }
    
                // minimum date from the 'not_before' field from layers of type 'origin'
                $values = array_filter($notBeforeValues, fn($value) => $value !== null);
                $array['date_min'] = $values ? min(array_map('intval', $values)) : null;
        
                // maximum date from the 'not_after' field from layers of type 'origin'
                $values = array_filter($notAfterValues, fn($value) => $value !== null);
                $array['date_max'] = $values ? max(array_map('intval', $values)) : null;
            }
        }

        $data = $this->getJsonData();

        // get all creators attached to this layer
        $array['names'] = collect($this->getRelatedAgentsAttribute())->pluck('pref_name');

        // get the source (i.e. state label from the parent layer and shelfmark from the parent parent's manuscript)
        $source = $this->getSource();
        $array['source'] = $source
            ? $source[0]['shelfmark'] . ($source[0]['state_label'] ? ' (' . $source[0]['state_label'] . ')' : '')
            : '';

        // language
        $languages = array_column($data['lang'], 'label');
        $array['language'] = implode(', ', $languages);

        // genre (i.e. genre of a work within a text unit's work witnesses)
        $genres = [];
        foreach ($data['work_wit'] as $item) {
            if (isset($item['work']['genre']) && is_array($item['work']['genre'])) {
                foreach ($item['work']['genre'] as $genre) {
                    if (isset($genre['label'])) {
                        $genres[] = $genre['label'];
                    }
                }
            }
        }
        $array['genre'] = implode(', ', $genres);

        /*
         * Apply default transformations if desired.
         *
         * https://www.algolia.com/doc/framework-integration/laravel/indexing/configure-searchable-data/?client=php#transformers
         */
        // $array = $this->transform($array);

        return $array;
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
TextUnit::initialize('text_unit');
