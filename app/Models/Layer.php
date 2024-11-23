<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Layer extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'ark',
        'identifier',
        'json',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
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
                'ark' => 'ARK',
                'identifier' => 'Identifier',
            ],
        ],
    ];

    protected $appends = [
        'text_units',
        'colophons',
        'para_except_colophons',
        'related_manuscripts',
        'associated_names_from_root',
        'associated_places_from_root',
        'associated_dates_from_root',
        'references',
        'bibliographies',
    ];
    
    public function getTextUnitsAttribute(): array
    {
        $textUnitsQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, '$.text_unit[*]') AS text_unit")
            ->where('id', $this->id)
            ->get();
        
        return $textUnitsQuery->map(function ($textUnit) {
            $textUnitData = json_decode($textUnit->text_unit, true);
            $textUnitObject = TextUnit::where('ark', $textUnitData['id'])->first();
            
            if ($textUnitObject) {
                $textUnitJson = json_decode($textUnitObject->json, true);
                
                return [
                    'label' => $textUnitData['label'],
                    'locus' => $textUnitData['locus'],
                    'lang' => $textUnitJson['lang'] ?? [],
                ];
            }
            
            return null;
        })->filter()->values()->toArray();
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
        $layerArk = $this->ark;
        
        $query = "
            WITH layer_text_units AS (
                SELECT DISTINCT jsonb_array_elements(layer.jsonb -> 'text_unit') ->> 'id' AS text_unit_ark
                FROM layers AS layer
                WHERE layer.jsonb ->> 'ark' = ?
            ),
            text_unit_works AS (
                SELECT DISTINCT jsonb_array_elements(tu.jsonb -> 'work_wit') -> 'work' ->> 'id' AS work_ark
                FROM text_units AS tu
                JOIN layer_text_units ON tu.jsonb ->> 'ark' = layer_text_units.text_unit_ark
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
            $layerArk,
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
    
    private function getParaByType(string $type = null): array
    {
        $query = '$.para[*]';
        if ($type !== null) {
            $query .= " ? (@.type.id == \"$type\")";
        } else {
            $query .= " ? (!(@.type.id == \"colophon\"))";
        }
        
        $parasQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, ?) AS para", [$query])
            ->where('id', $this->id)
            ->get();
        
        return $parasQuery->map(function ($para) {
            $paraData = json_decode($para->para, true);
            
            if (isset($paraData['assoc_name']) && is_array($paraData['assoc_name'])) {
                foreach ($paraData['assoc_name'] as &$assocName) {
                    $agent = Agent::where('ark', $assocName['id'])->first();
                    $assocName['pref_name'] = $agent ? $agent->pref_name : null;
                }
            }
            
            if (isset($paraData['assoc_place']) && is_array($paraData['assoc_place'])) {
                foreach ($paraData['assoc_place'] as &$assocPlace) {
                    $place = Place::where('ark', $assocPlace['id'])->first();
                    $assocPlace['pref_name'] = $place ? $place->pref_name : null;
                }
            }
            
            return $paraData;
        })->toArray();
    }
    
    public function getColophonsAttribute(): array
    {
        return $this->getParaByType('colophon');
    }
    
    public function getParaExceptColophonsAttribute(): array
    {
        return $this->getParaByType();
    }
    
    public function getRelatedManuscriptsAttribute(): array
    {
        $relatedManuscripts = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, '$.related_mss[*]') AS related_ms")
            ->where('id', $this->id)
            ->get();
        
        return $relatedManuscripts->map(function ($relatedMs) {
            return json_decode($relatedMs->related_ms, true); // Decode the entire JSON node
        })->toArray();
    }
    
    public function getAssociatedNamesFromRootAttribute(): array
    {
        $associatedNames = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, '$.assoc_name[*]') AS assoc_name")
            ->where('id', $this->id)
            ->get();
        
        return $associatedNames->map(function ($assocName) {
            $assocNameData = json_decode($assocName->assoc_name, true);
            
            if (isset($assocNameData['id'])) {
                $agent = Agent::where('ark', $assocNameData['id'])->first();
                $assocNameData['pref_name'] = $agent ? $agent->pref_name : null;
            } else {
                $assocNameData['pref_name'] = null;
            }
            
            return $assocNameData;
        })->toArray();
    }
    
    public function getAssociatedPlacesFromRootAttribute(): array
    {
        $associatedPlacesQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, '$.assoc_place[*]') AS assoc_place")
            ->where('id', $this->id)
            ->get();
        
        return $associatedPlacesQuery->map(function ($assocPlace) {
            $assocPlaceData = json_decode($assocPlace->assoc_place, true);
            
            if (isset($assocPlaceData['id'])) {
                $place = Place::where('ark', $assocPlaceData['id'])->first();
                $assocPlaceData['pref_name'] = $place ? $place->pref_name : null;
            } else {
                $assocPlaceData['pref_name'] = null;
            }
            
            return $assocPlaceData;
        })->toArray();
    }
    
    public function getAssociatedDatesFromRootAttribute(): array
    {
        $associatedDatesQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, '$.assoc_date[*]') AS assoc_date")
            ->where('id', $this->id)
            ->get();
        
        return $associatedDatesQuery->map(function ($assocName) {
            $assocDateData = json_decode($assocName->assoc_date, true);
            return $assocDateData;
        })->toArray();
    }
    
    private function getReferencesByType(string $type): array
    {
        $query = '$.bib[*] ? (@.type.id == "' . $type . '")';
        
        $referencesQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, ?) AS reference", [$query])
            ->where('id', $this->id)
            ->get();
        
        return $referencesQuery->map(function ($reference) {
            $referenceData = json_decode($reference->reference, true);
            
            if (isset($referenceData['id'])) {
                $ref = Reference::where('id', $referenceData['id'])->first();
                if ($ref) {
                    $referenceData['short_title'] = $ref->short_title;
                    $referenceData['formatted_citation'] = $ref->formatted_citation;
                }
            }
            
            $referenceData['alt_shelf'] = $referenceData['alt_shelf'] ?? null;
            $referenceData['range'] = $referenceData['range'] ?? null;
            $referenceData['url'] = $referenceData['url'] ?? null;
            $referenceData['note'] = $referenceData['note'] ?? [];
            
            return $referenceData;
        })->toArray();
    }
    
    public function getReferencesAttribute(): array
    {
        return $this->getReferencesByType('ref');
    }
    
    public function getBibliographiesAttribute(): array
    {
        return $this->getReferencesByType('cite');
    }
    
    
    
    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $data = $this->getJsonData();

        // Source field (only manuscripts, not other layers)
        $array['source'] = $this->getSourceIdentifiers();

        $array['ark'] = $this->ark ?? null;
        $array['identifier'] = $this->identifier ?? null;
        $array['extent'] = $data['extent'] ?? null;
        
        $array['state'] = isset($data['state']['label']) ? $data['state']['label'] : null;

        // associated dates
        $array['dates'] = [];
        if (isset($data['assoc_date'])) {
            foreach($data['assoc_date'] as $date) {
                $array['dates'][] = isset($date['type']['id']) && $date['type']['id'] === 'origin'
                    ? ($date['value'] ?? null)
                    : null;

                $notBeforeValues = [];
                $notAfterValues = [];
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

        // script
        $array['script'] = [];
        foreach($data['writing'] as $writing) {
            foreach($writing['script'] as $script) {
                $array['script'][] = isset($script['label']) ? $script['label'] : null;
            }
        }

        // writing system
        $array['script'] = [];
        foreach($data['writing'] as $writing) {
            foreach($writing['script'] as $script) {
                $array['writing_system'][] = isset($script['writing_system']) ? $script['writing_system'] : null;
            }
        }

        // features
        $array['features'] = [];
        if (isset($data['features'])) {
            foreach($data['features'] as $feature) {
                $array['features'][] = isset($feature['label']) ? $feature['label'] : null;
            }
        }

        // get all creators attached to this layer
        $array['names'] = collect($this->getRelatedAgentsAttribute())->pluck('pref_name');

        /*
         * Apply default transformations if desired.
         *
         * https://www.algolia.com/doc/framework-integration/laravel/indexing/configure-searchable-data/?client=php#transformers
         */
        // $array = $this->transform($array);

        return $array;
    }

    /**
     * Get the source manuscripts' identifiers.
     *
     * @return string|null
     */
    public function getSourceIdentifiers(): ?string
    {
        $data = $this->getJsonData();

        if (empty($data['parent'])) {
            return null;
        }

        $sourceManuscripts = Manuscript::whereIn('ark', $data['parent'])->get();
        $identifiers = $sourceManuscripts->pluck('identifier')->toArray();

        return !empty($identifiers) ? implode(', ', $identifiers) : null;
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Layer::initialize('layer');
