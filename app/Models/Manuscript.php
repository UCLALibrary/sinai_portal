<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use App\Traits\RelatedBibliographies;
use App\Traits\RelatedLayers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Manuscript extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities, RelatedLayers, RelatedBibliographies;
    
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
            'sort' => [
                'field' => 'identifier',
                'direction' => 'asc',
            ],
            'columns' => [
                'identifier' => 'Identifier',
                'ark' => 'ARK',
            ],
        ],
    ];
    
    protected $appends = [
        'assoc_names',
        'assoc_names_from_para',
        'assoc_names_from_parts_para',
        'related_overtext_layers',
        'assoc_dates_overview',
        'lang_from_parts_layers_text_units',
        'lang_from_layers_text_units',
        'part',
        'part_para',
        'part_layer_overtext',
        'layer_overtext',
        'part_layer_undertext',
        'layer_undertext',
        'part_layer_guest',
        'layer_guest',
        'para',
        'references',
        'bibliographies',
        'related_digital_versions',
        'related_text_units',
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
    
    /**
     * Returns all dates ('assoc_date.value' field) of type 'origin' for overtext layers.
     *
     * @return array
     */
    public function getAssocDatesOverviewAttribute(): array
    {
        $overtextLayerArks = DB::table('manuscripts')
            ->selectRaw("jsonb_path_query_array(jsonb, '$.part[*].layer[*] ? (@.type.id == \"overtext\").id') AS arks")
            ->where('id', $this->id)
            ->first();
        
        $overtextLayerArks = json_decode($overtextLayerArks->arks, true);
        
        if (empty($overtextLayerArks)) {
            return [];
        }
        
        $overtextLayers = $this->getLayersByArks($overtextLayerArks);
        
        $dates = [];
        foreach ($overtextLayers as $layer) {
            if (isset($layer['assoc_date']) && is_array($layer['assoc_date'])) {
                foreach ($layer['assoc_date'] as $assocDate) {
                    if (isset($assocDate['type']['id']) && $assocDate['type']['id'] === 'origin' && isset($assocDate['value'])) {
                        $dates[] = $assocDate['value'];
                    }
                }
            }
        }
        
        return $dates;
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
    
    public function getPartAttribute(): array
    {
        $partsQuery = DB::table('manuscripts')
            ->selectRaw("jsonb_path_query(jsonb, '$.part[*]') AS part")
            ->where('id', $this->id)
            ->get();
        
        return $partsQuery->map(function ($part) {
            $partJson = json_decode($part->part, true);
            
            if (!$partJson || !isset($partJson['layer'])) {
                return $partJson;
            }
            
            $partJson['layer'] = collect($partJson['layer'])->map(function ($layer) {
                $enrichedLayers = $this->getRelatedLayersWithTextUnits(
                    'manuscripts',
                    $this->id,
                    '$.part[*].layer[*] ? (@.type.id == "overtext" && @.id == "' . addslashes($layer['id']) . '")'
                );
                
                return $enrichedLayers[0] ?? $layer;
            })->toArray();
            
            return $partJson;
        })->toArray();
    }
    
    public function getPartParaAttribute(): array
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
    
    public function getPartLayerOvertextAttribute(): array
    {
        return $this->getRelatedLayersWithTextUnits('manuscripts', $this->id, '$.part[*].layer[*] ? (@.type.id == "overtext")');
    }
    
    public function getLayerOvertextAttribute(): array
    {
        return $this->getRelatedLayersWithTextUnits('manuscripts', $this->id, '$.layer[*] ? (@.type.id == "overtext")');
    }
    
    public function getPartLayerUndertextAttribute(): array
    {
        return $this->getRelatedLayersWithTextUnits('manuscripts', $this->id, '$.part[*].layer[*] ? (@.type.id == "undertext")');
    }
    
    public function getLayerUndertextAttribute(): array
    {
        return $this->getRelatedLayersWithTextUnits('manuscripts', $this->id, '$.layer[*] ? (@.type.id == "undertext")');
    }
    
    public function getPartLayerGuestAttribute(): array
    {
        return $this->getRelatedLayersWithTextUnits('manuscripts', $this->id, '$.part[*].layer[*] ? (@.type.id == "guest")');
    }
    
    public function getLayerGuestAttribute(): array
    {
        return $this->getRelatedLayersWithTextUnits('manuscripts', $this->id, '$.layer[*] ? (@.type.id == "guest")');
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
    
    public function getReferencesAttribute(): array
    {
        return $this->getReferencesByType('manuscripts', $this->id, 'ref');
    }
    
    public function getBibliographiesAttribute(): array
    {
        return $this->getReferencesByType('manuscripts', $this->id, 'cite');
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
        return $this->getRelatedLayersWithTextUnits('manuscripts', $this->id, 'strict $.**.layer[*]');
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
