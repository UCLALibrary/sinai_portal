<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Manuscript extends Model
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
        'type',
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
            $data['type']['label'],
            $data['shelfmark'],
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
        'assoc_names',
        // 'assoc_names_from_root',
        'assoc_names_from_para',
        'assoc_names_from_parts_para',
        // 'related_places',
        'related_overtext_layers',
        'assoc_dates_overview',
        'assoc_dates_from_layers',
        'assoc_places_from_layers',
        'lang_from_parts_layers_text_units',
        'lang_from_layers_text_units',
        'parts',
        'para',
        'related_references',
        'related_bibliographies',
        'related_digital_versions',
        'related_text_units'
    ];
    

    /**
     * Returns top-level "assoc_name" objects, "assoc_name" objects from the top-level "para" 
     * objects, and "assoc_name" objects from the "para" objects nested within "part" objects.
     *
     * @return array
     */
    public function getAssocNamesAttribute(): array
    {
        $names = array_merge(
            $this->getAssocNamesFromRootAttribute(),
            $this->getAssocNamesFromPartsParaAttribute(),
            $this->getAssocNamesFromParaAttribute(),
        );
        return $names;
    }

    /**
     * Returns top-level "assoc_name" objects.
     *
     * @return array
     */
    public function getAssocNamesFromRootAttribute(): array
    {
        $query = "
            SELECT
                substring(agents.ark from '[^/]+$') AS id,
                agents.jsonb->>'pref_name' AS pref_name,
                assoc_name_elem->'role'->>'label' AS role_label
            FROM
                manuscripts
                CROSS JOIN LATERAL jsonb_array_elements(jsonb->'assoc_name') AS assoc_name_elem
                JOIN agents ON assoc_name_elem->>'id' = agents.ark
            WHERE
                assoc_name_elem IS NOT NULL
            AND manuscripts.ark = :manuscript_ark;
        ";
        
        $bindings = [
            'manuscript_ark' => $this->ark,
        ];

        return DB::select($query, $bindings);
    }

    /**
     * Returns the "assoc_name" objects from the top-level "para" objects.
     *
     * @return array
     */
    public function getAssocNamesFromParaAttribute(): array
    {
        $query = "
            SELECT
                substring(agents.ark from '[^/]+$') AS id,
                agents.jsonb->>'pref_name' AS pref_name,
                assoc_name_elem->'role'->>'label' AS role_label,
                assoc_name_elem->>'as_written' AS as_written,
                assoc_name_elem->'note' AS note
            FROM
                manuscripts
                CROSS JOIN LATERAL jsonb_array_elements(jsonb->'para') AS para_elem
                LEFT JOIN LATERAL jsonb_array_elements(para_elem->'assoc_name') AS assoc_name_elem ON TRUE
                JOIN agents ON assoc_name_elem->>'id' = agents.ark
            WHERE
                assoc_name_elem IS NOT NULL
            AND manuscripts.ark = :manuscript_ark;
        ";
        
        $bindings = [
            'manuscript_ark' => $this->ark,
        ];

        return DB::select($query, $bindings);
    }

    /**
     * Returns the "assoc_name" objects from the "para" objects nested within "part" objects.
     *
     * @return array
     */
    public function getAssocNamesFromPartsParaAttribute(): array
    {
        $query = "
            SELECT
                substring(agents.ark from '[^/]+$') AS id,
                agents.jsonb->>'pref_name' AS pref_name,
                assoc_name_elem->'role'->>'label' AS role_label
            FROM
                manuscripts
                CROSS JOIN LATERAL jsonb_array_elements(jsonb->'part') AS part_elem
                LEFT JOIN LATERAL jsonb_array_elements(part_elem->'para') AS para_elem ON TRUE
                LEFT JOIN LATERAL jsonb_array_elements(para_elem->'assoc_name') AS assoc_name_elem ON TRUE
                JOIN agents ON assoc_name_elem->>'id' = agents.ark
            WHERE
                assoc_name_elem IS NOT NULL
            AND manuscripts.ark = :manuscript_ark;
        ";
        
        $bindings = [
            'manuscript_ark' => $this->ark,
        ];

        return DB::select($query, $bindings);
    }
    
    public function getRelatedOvertextLayersAttribute(): array
    {
        return $this->getRelatedEntities(
            'layer',
            Layer::class,
            function ($item) {
                return isset($item['type']['id']) && $item['type']['id'] === 'overtext';
            },
            function ($layer, $item) {
                return [
                    'id' => $layer->id,
                    'json' => json_decode($layer->json)
                ];
            })->toArray();
    }
    
    public function getAssocDatesOverviewAttribute(): array
    {
        $query = "
        SELECT DISTINCT jsonb_path_query(jsonb, :jsonPath) AS assoc_date_value
        FROM layers
        WHERE jsonb_path_exists(jsonb, :existsJsonPath, :vars);
    ";
        
        $bindings = [
            'jsonPath' => '$.**.assoc_date[*] ? (@.type.id == "origin").value',
            'existsJsonPath' => '$.**.parent ? (@ == $manuscript_ark)',
            'vars' => json_encode(['manuscript_ark' => $this->ark]),
        ];
        
        $dates = DB::select($query, $bindings);
        
        return array_map(function ($row) {
            return json_decode($row->assoc_date_value);
        }, $dates);
    }

    public function getAssocDatesFromLayersAttribute(): array
    {
        $query = "
            SELECT DISTINCT 
                id,
                jsonb_path_query(jsonb, :assocDateValuePath) AS assoc_date_value,
                jsonb_path_query(jsonb, :assocDateNotBeforePath) AS not_before,
                jsonb_path_query(jsonb, :assocDateNotAfterPath) AS not_after
            FROM layers
            WHERE jsonb_path_exists(jsonb, :existsJsonPath, :vars);
        ";

        $bindings = [
            'assocDateValuePath' => '$.**.assoc_date[*] ? (@.type.id == "origin").value',
            'assocDateNotBeforePath' => '$.**.assoc_date[*] ? (@.type.id == "origin").iso.not_before',
            'assocDateNotAfterPath' => '$.**.assoc_date[*] ? (@.type.id == "origin").iso.not_after',
            'existsJsonPath' => '$.**.parent ? (@ == $manuscript_ark)',
            'vars' => json_encode(['manuscript_ark' => $this->ark]),
        ];

        $dates = DB::select($query, $bindings);

        return array_map(function ($row) {
            return [
                'id' => $row->id,
                'assoc_date_value' => json_decode($row->assoc_date_value),
                'not_before' => json_decode($row->not_before),
                'not_after' => json_decode($row->not_after),
            ];
        }, $dates);
    }

    public function getAssocPlacesFromLayersAttribute(): array
    {
        $query = "
        SELECT DISTINCT id, jsonb_path_query(jsonb, :jsonPath) AS assoc_place_as_written
        FROM layers
        WHERE jsonb_path_exists(jsonb, :existsJsonPath, :vars);
    ";
        
        $bindings = [
            'jsonPath' => '$.**.assoc_place[*] ? (@.event.id == "origin").as_written',
            'existsJsonPath' => '$.**.parent ? (@ == $manuscript_ark)',
            'vars' => json_encode(['manuscript_ark' => $this->ark]),
        ];
        
        $dates = DB::select($query, $bindings);
        
        return array_map(function ($row) {
            return [
                'id' => $row->id,
                'assoc_place_value' => json_decode($row->assoc_place_as_written)
            ];
        }, $dates);
    }
    
    public function getLangFromPartsLayersTextUnitsAttribute(): array
    {
        $jsonData = $this->getJsonData();
        $layersJson = $jsonData['part'] ?? [];
        
        $layerArks = [];
        foreach ($layersJson as $part) {
            if (isset($part['layer'])) {
                foreach ($part['layer'] as $layer) {
                    $layerArks[] = $layer['id'];
                }
            }
        }
        
        $layers = Layer::whereIn('ark', $layerArks)->get();
        $languages = [];
        
        foreach ($layers as $layer) {
            $layerJson = json_decode($layer->jsonb, true);
            if (isset($layerJson['text_unit'])) {
                
                foreach ($layerJson['text_unit'] as $textUnit) {
                    $textUnitModel = TextUnit::where('ark', $textUnit['id'])->first();
                    
                    if ($textUnitModel) {
                        $textUnitJson = json_decode($textUnitModel->jsonb, true);
                        
                        if (isset($textUnitJson['lang'])) {
                            
                            foreach ($textUnitJson['lang'] as $lang) {
                                $languages[] = [
                                    'layer_id' => $layer->id,
                                    'lang_label' => $lang['label']
                                ];
                            }
                            
                        }
                        
                    }
                    
                }
                
            }
        }
        
        return $languages;
    }
    
    public function getLangFromLayersTextUnitsAttribute(): array
    {
    $jsonData = $this->getJsonData();
    $layersJson = $jsonData['layer'] ?? [];
    
    $layerArks = array_column($layersJson, 'id');
    
    $layers = Layer::whereIn('ark', $layerArks)->get();
    $languages = [];
    
    foreach ($layers as $layer) {
        $layerJson = json_decode($layer->jsonb, true);
        if (isset($layerJson['text_unit'])) {
            foreach ($layerJson['text_unit'] as $textUnit) {
                $textUnitModel = TextUnit::where('ark', $textUnit['id'])->first();
                if ($textUnitModel) {
                    $textUnitJson = json_decode($textUnitModel->jsonb, true);
                    if (isset($textUnitJson['lang'])) {
                        foreach ($textUnitJson['lang'] as $lang) {
                            $languages[] = [
                                'layer_id' => $layerJson['ark'],
                                'lang_label' => $lang['label']
                            ];
                        }
                    }
                }
            }
        }
    }
    
    return $languages;
    }
    
    public function getPartsAttribute(): array
    {
        $jsonData = $this->getJsonData();
        $parts = $jsonData['part'] ?? [];
        
        foreach ($parts as &$part) {
            if (isset($part['para'])) {
                foreach ($part['para'] as &$para) {
                    
                    if (isset($para['assoc_name'])) {
                        foreach ($para['assoc_name'] as &$assocName) {
                            $agent = Agent::where('ark', $assocName['id'])->first();
                            
                            if ($agent) {
                                $assocName['pref_name'] = $agent->pref_name;
                            }
                        }
                        unset($assocName);
                    }
                    
                    if (isset($para['assoc_place'])) {
                        foreach ($para['assoc_place'] as &$assocPlace) {
                            $place = Place::where('ark', $assocPlace['id'])->first();
                            
                            if ($place) {
                                $assocPlace['pref_name'] = $place->pref_name;
                            }
                        }
                        unset($assocPlace);
                    }
                }
                unset($para);
            }
        }
        unset($part);
        
        return $parts;
    }
    
    public function getParaAttribute(): array
    {
        $jsonData = $this->getJsonData();
        $paracontent = $jsonData['para'] ?? [];
        
        foreach ($paracontent as &$para) {
            
            if (isset($para['assoc_name'])) {
                foreach ($para['assoc_name'] as &$assocName) {
                    $agent = Agent::where('ark', $assocName['id'])->first();
                    
                    if ($agent) {
                        $assocName['pref_name'] = $agent->pref_name;
                    }
                }
                unset($assocName);
            }
            
            if (isset($para['assoc_place'])) {
                foreach ($para['assoc_place'] as &$assocPlace) {
                    $place = Place::where('ark', $assocPlace['id'])->first();
                    
                    if ($place) {
                        $assocPlace['pref_name'] = $place->pref_name;
                    }
                }
                unset($assocPlace);
            }
        }
        unset($para);
        
        return $paracontent;
    }
    
    public function getRelatedPlacesAttribute(): array
    {
        return $this->getRelatedEntities(
            'assoc_place',
            Place::class,
            null,
            function ($place, $item) {
                return [
                    'id' => $place->id,
                    'as_written' => $item['as_written'] ?? null,
                    'pref_name' => $place->pref_name,
                    'event' => $item['note'],
                    'note' => $item['note'] ?? [],
                ];
            })->toArray();
    }
    
    public function getRelatedReferencesAttribute(): array
    {
        return $this->getRelatedEntities(
            'bib',
            Reference::class,
            function ($item) {
                return isset($item['type']['id']) && $item['type']['id'] === 'ref';
            },
            function ($bibItem, $item) {
                return [
                    'id' => $bibItem->id,
                    'short_title' => $bibItem->short_title,
                    'formatted_citation' => $bibItem->formatted_citation,
                    'alt_shelf' => $item['alt_shelf'] ?? null,
                    'range' => $item['range'] ?? null,
                    'note' => $item['note'] ?? [],
                ];
            }
        )->toArray();
    }
    
    public function getRelatedBibliographiesAttribute(): array
    {
        return $this->getRelatedEntities(
            'bib',
            Reference::class,
            function ($item) {
                return isset($item['type']['id']) && $item['type']['id'] === 'cite';
            },
            function ($bibItem, $item) {
                return [
                    'id' => $bibItem->id,
                    'short_title' => $bibItem->short_title,
                    'formatted_citation' => $bibItem->formatted_citation,
                    'range' => $item['range'] ?? null,
                    'url' => $item['url'] ?? null,
                    'note' => $item['note'] ?? [],
                ];
            }
        )->toArray();
    }
    
    public function getRelatedDigitalVersionsAttribute(): array
    {
        return $this->getRelatedEntities(
            'bib',
            Reference::class,
            function ($item) {
                return isset($item['type']['id']) && $item['type']['id'] === 'otherdigversion';
            },
            function ($bibItem, $item) {
                return [
                    'id' => $bibItem->id,
                    'short_title' => $bibItem->short_title,
                    'formatted_citation' => $bibItem->formatted_citation,
                    'range' => $item['range'] ?? null,
                    'url' => $item['url'] ?? null,
                    'note' => $item['note'] ?? [],
                ];
            }
        )->toArray();
    }
    
    public function getRelatedTextUnitsAttribute(): array
    {
        $jsonData = $this->getJsonData();
        $layersJson = $jsonData['layer'] ?? [];
        $layerArks = array_column($layersJson, 'id');
        
        $layers = Layer::whereIn('ark', $layerArks)->get();
        
        $textUnitsArk = [];
        foreach ($layers as $layer) {
            $layerJson = json_decode($layer->jsonb, true);
            
            foreach ($layerJson['text_unit'] as $textUnit) {
                $textUnitsArk[] = $textUnit['id'];
            }
        }
        
        $textUnits = TextUnit::whereIn('ark', $textUnitsArk)->get();
        return $textUnits->toArray();
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
        
        $array['ark'] = $this->ark ?? null;
        $array['identifier'] = $this->identifier ?? null;
        $array['type'] = $this->type ?? null;
        $array['extent'] = $data['extent'] ?? null;
        
        $array['state'] = isset($data['state']['label']) ? $data['state']['label'] : null;
        
        // support
        $array['support'] = [];
        foreach ($data['part'] as $part) {
            foreach ($part['support'] as $support) {
                $array['support'][] = isset($support['label']) ? $support['label'] : null;
            }
        }
        
        // features
        $array['features'] = [];
        if (isset($data['features'])) {
            foreach ($data['features'] as $feature) {
                $array['features'][] = isset($feature['label']) ? $feature['label'] : null;
            }
        }
        
        // location (repository, collection)
        $array['repository'] = [];
        $array['collection'] = [];
        if (isset($data['location'])) {
            foreach ($data['location'] as $location) {
                $array['repository'] = isset($location['repository']) ? $location['repository'] : null;
                $array['collection'] = isset($location['collection']) ? $location['collection'] : null;
            }
        }
        
        // get all the associated names
        $array['names'] = collect($this->getAssocNamesAttribute())->pluck('pref_name');

        // get dates attached to this manuscript for display in list view
        $array['dates'] = $this->assoc_dates_overview;

        // minimum date from the 'not_before' field from layers of type 'origin'
        $notBeforeValues = array_filter(array_column($this->getAssocDatesFromLayersAttribute(), 'not_before'));
        $values = array_filter($notBeforeValues, fn($value) => $value !== null);
        $array['date_min'] = $values ? min(array_map('intval', $values)) : null;

        // maximum date from the 'not_after' field from layers of type 'origin'
        $notAfterValues = array_filter(array_column($this->getAssocDatesFromLayersAttribute(), 'not_after'));
        $values = array_filter($notAfterValues, fn($value) => $value !== null);
        $array['date_max'] = $values ? max(array_map('intval', $values)) : null;

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
Manuscript::initialize('ms-obj');
