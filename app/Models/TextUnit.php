<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use App\Traits\RelatedBibliographies;
use App\Traits\RelatedPara;
use App\Traits\RelatedTextUnits;
use App\Traits\RelatedWorkWitnesses;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class TextUnit extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities, RelatedBibliographies, RelatedPara, RelatedWorkWitnesses, RelatedTextUnits;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'ark',
        'label',
        'json',
    ];
    
    public function getFillableFields($data, $json) {
        return array_combine($this->fillable, [
            basename($data['ark']),  // use the trailing ark segment as the id
            $data['ark'],
            $data['label'],
            $json,
        ]);
    }
    
    public static $config = [
        'index' => [
            'sort' => [
                'field' => 'label',
                'direction' => 'asc',
            ],
            'columns' => [
                'label' => 'Label',
                'ark' => 'ARK',
            ],
        ],
    ];
    
    protected $appends = [
        'source',
        'work_witnesses',
        'para',
        'editions',
        'translations',
        'references',
        'bibliographies',
        'sidebar_names',
        'sidebar_works',
        'sidebar_reconstructions',
        'sidebar_reconstructed_from'
    ];
    
    /**
     * Returns the "source" for the text unit, which consists of the state label of its parent
     * layer and the shelfmark of its parent layer's manuscript.
     *
     * @return array
     */
    public function getSourceAttribute(): array {
        $query = "
            SELECT
                l.jsonb->'state'->>'label' AS state_label,
                m.jsonb->>'shelfmark' AS shelfmark,
                tu.jsonb->>'locus' AS locus
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
        
        $result = DB::selectOne($query, $bindings);
        return (array) $result;
    }
    
    public function getWorkWitnessesAttribute(): array {
        return $this->getRelatedWorkWitnesses('text_units', $this->id, '$.work_wit[*]');
    }
    
    public function getParaAttribute(): array {
        return $this->getParaByQuery('text_units', $this->id, '$.para[*]');
    }
    
    public function getEditionsAttribute(): array {
        return $this->getReferencesByType('text_units', $this->id, 'edition');
    }
    
    public function getTranslationsAttribute(): array {
        return $this->getReferencesByType('text_units', $this->id, 'translation');
    }
    
    public function getReferencesAttribute(): array {
        return $this->getReferencesByType('text_units', $this->id, 'ref');
    }
    
    public function getBibliographiesAttribute(): array {
        return $this->getReferencesByType('text_units', $this->id, 'cite');
    }
    
    public function getSidebarNamesAttribute(): array {
        return $this->getRelatedAgents('text_units', $this->id, 'strict $.**.assoc_name[*]');
    }
    
    public function getSidebarWorksAttribute(): array {
        
        $allWorkArksQuery = DB::table($this->table)
            ->selectRaw('jsonb_agg(work_arks) AS all_work_arks')
            ->fromSub(function ($query) {
                $query->selectRaw("jsonb_path_query(jsonb, '$.work_wit[*].work.id') AS work_arks")
                    ->from($this->table)
                    ->where('id', $this->id)
                    ->unionAll(
                        DB::table($this->table)
                            ->selectRaw("jsonb_path_query(jsonb, '$.work_wit[*].contents[*].work_id') AS work_arks")
                            ->where('id', $this->id)
                    );
            }, 'combined')
            ->first();
        
        $allWorkArks = json_decode($allWorkArksQuery->all_work_arks);
        $works = [];
        
        if ($allWorkArks) {
            $works = $this->getWorksByArks($allWorkArks);
        }
        
        return $works;
    }
    
    public function getSidebarReconstructionsAttribute(): array {
        $textUnitsQuery = DB::table('text_units')
            ->select('ark')
            ->whereRaw("jsonb_path_exists(jsonb, '$.reconstructed_from[*] ? (@ == \"$this->ark\")')")
            ->pluck('ark')
            ->toArray();
        
        return $this->getTextUnitsByArks($textUnitsQuery);
    }
    
    public function getSidebarReconstructedFromAttribute(): array {
        $textUnitsQuery = DB::table('text_units')
            ->selectRaw("jsonb_path_query_array(jsonb, '$.reconstructed_from[*]') AS reconstructed_from")
            ->whereRaw("jsonb_extract_path_text(jsonb, 'reconstruction') = 'true'")
            ->where('id', $this->id)
            ->first();
        
        $reconstructedFromArks = $textUnitsQuery ? json_decode($textUnitsQuery->reconstructed_from, true) : [];
        return $this->getTextUnitsByArks($reconstructedFromArks);
    }
    
    /**
     * Returns the genres for the text unit that are embedded within works of its work
     * witnesses -AND- genres within its referenced work.
     *
     * @return array
     */
    public function getGenres(): array {
        $query = "
            -- Extracts genres from the embedded work metadata
            SELECT
                genre_elem.value->>'id' AS id,
                genre_elem.value->>'label' AS label
            FROM
                text_units tu
            -- unnest the 'work_wit' array from the text unit
            JOIN LATERAL jsonb_array_elements(tu.jsonb->'work_wit') AS work_wit_elem(value) ON TRUE
            -- access the 'work' object within each 'work_wit' element
            JOIN LATERAL (SELECT work_wit_elem.value->'work' AS work_obj) AS work_lateral ON TRUE
            -- unnest the 'genre' array within the 'work' object
            JOIN LATERAL jsonb_array_elements(work_lateral.work_obj->'genre') AS genre_elem(value) ON TRUE
            WHERE
                tu.jsonb->>'ark' = :text_unit_ark

            UNION ALL

            -- Extracts genres from the referenced work
            SELECT
                genre_elem.value->>'id' AS id,
                genre_elem.value->>'label' AS label
            FROM
                text_units tu
            -- unnest the 'work_wit' array from the text unit
            JOIN LATERAL jsonb_array_elements(tu.jsonb->'work_wit') AS work_wit_elem(value) ON TRUE
            -- access the 'work' object within each 'work_wit' element
            JOIN LATERAL (SELECT work_wit_elem.value->'work' AS work_obj) AS work_lateral ON TRUE
            -- extract the 'id' from the 'work' object
            JOIN LATERAL (SELECT work_lateral.work_obj->>'id' AS work_id_text) AS work_id_lateral ON TRUE
            -- join with the 'work' table using the extracted 'id'
            JOIN works w ON w.ark = work_id_lateral.work_id_text
            -- unnest the 'genre' array from the 'work' data
            JOIN LATERAL jsonb_array_elements(w.jsonb->'genre') AS genre_elem(value) ON TRUE
            WHERE
                tu.jsonb->>'ark' = :text_unit_ark;
        ";
        
        $bindings = [
            'text_unit_ark' => $this->ark,
        ];
        
        $rows = DB::select($query, $bindings);
        $results = array_map(function ($item) {
            return (array)$item;
        }, $rows);
        
        return $results;
    }
    
    /**
     * Accessor to include related agents when the model is serialized.
     *
     * @return array
     */
    public function getRelatedAgentsAttribute(): array {
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
    
    public function getConnectedAgentCreatorNames() {
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
    
    public function getRelatedWorksAttribute() {
        $query = "
            WITH text_unit_works AS (
                SELECT DISTINCT
                    jsonb_array_elements(tu.jsonb -> 'work_wit') -> 'work' ->> 'id' AS work_ark
                FROM text_units AS tu
                WHERE tu.jsonb ->> 'ark' = ?
            )
            SELECT
                w.id,
                w.jsonb ->> 'pref_title' AS pref_title,
                w.jsonb ->> 'ark' AS work_ark
            FROM works AS w
            JOIN text_unit_works AS tuw ON w.jsonb ->> 'ark' = tuw.work_ark;
        ";
        
        $bindings = [
            $this->ark,
        ];
        
        $results = DB::select($query, $bindings);
        
        $relatedWorks = array_map(function ($row) {
            return [
                'id' => $row->id,
                'pref_title' => $row->pref_title,
                'work_ark' => $row->work_ark,
            ];
        }, $results);
        
        return $relatedWorks;
    }
    
    /**
     * Get the parent layers.
     *
     * @return string|null
     */
    public function getParentLayers() {
        $data = $this->getJsonData();
        
        return !empty($data['parent'])
            ? Layer::whereIn('ark', $data['parent'])->get()
            : null;
    }

    /**
     * Reindex parent resource records.
     *
     * @return void
     */
    public function reindexParentResources()
    {
        if ($parentArks = $this->getJsonData()['parent']) {
            foreach ($parentArks as $parentArk) {
                $layer = Layer::where('ark', $parentArk)->first();
                if ($layer) {
                    $layer->searchable();
                }
            }
        }
        return;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array {
        $array = $this->toArray();

        unset($array['json']);
        unset($array['jsonb']);

        $array['ark'] = $this->ark ?? null;
        $array['label'] = $this->label ?? null;
        
        // min and max date from the 'assoc_date' field from layers of type 'origin'
        foreach ($this->getParentLayers() as $layer) {
            $data = $layer->getJsonData();
            
            if (isset($data['assoc_date'])) {
                $notBeforeValues = [];
                $notAfterValues = [];
                foreach ($data['assoc_date'] as $date) {
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
        
        // get the source (i.e. state label from the parent layer and shelfmark from the parent's parent manuscript)
        $source = $this->getSourceAttribute();
        $array['source'] = $source
            ? $source['shelfmark'] . ($source['state_label'] ? ' (' . $source['state_label'] . ')' : '')
            : '';
        
        // languages
        $array['languages'] = array_column($data['lang'], 'label');
        
        // genres (i.e. genres embedded within works of its work witnesses -AND- genres within its referenced work)
        $genres = array_column($this->getGenres(), 'label');
        $array['genres'] = array_unique($genres);
        
        // features
        $array['features'] = [];
        if (isset($data['features'])) {
            foreach ($data['features'] as $feature) {
                $array['features'][] = isset($feature['label']) ? $feature['label'] : null;
            }
        }
        
        // get the related works
        $array['works'] = collect($this->getRelatedWorksAttribute())->pluck('pref_title')->unique()->values()->all();
        
        
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
