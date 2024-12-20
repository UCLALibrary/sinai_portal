<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use App\Traits\RelatedBibliographies;
use App\Traits\RelatedLayers;
use App\Traits\RelatedPara;
use App\Traits\RelatedTextUnits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Layer extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities, RelatedTextUnits, RelatedLayers, RelatedBibliographies, RelatedPara;
    
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
        'text_units',
        'primary_languages',
        'colophons',
        'para_except_colophons',
        'related_manuscripts',
        'associated_names_from_root',
        'associated_places_from_root',
        'associated_dates_from_root',
        'references',
        'bibliographies',
        'works',
        'all_associated_names',
        'all_associated_places',
        'reconstructed_manuscripts',
        'reconstructed_layers',
        'reconstructed_from_layers'
    ];
    
    public function getTextUnitsAttribute(): array
    {
        return $this->getRelatedTextUnits('layers', $this->id, '$.text_unit[*]');
    }
    
    public function getPrimaryLanguagesAttribute() {
        $textUnits = $this->getTextUnitsAttribute();
        
        $languages = collect($textUnits)
            ->flatMap(fn($textUnit) => $textUnit['text_unit']['lang'] ?? [])
            ->unique('id')
            ->values()
            ->toArray();
        
        return $languages;
    }
    
    /**
     * Accessor to include related agents when the model is serialized.
     *
     * @return array
     */
    public function getRelatedAgentsAttribute(): array
    {
        return $this->getRelatedEntities(
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
    }
    
    public function getColophonsAttribute(): array
    {
        return $this->getParaByQuery('layers', $this->id, '$.para[*] ? (@.type.id == "colophon")');
    }
    
    public function getParaExceptColophonsAttribute(): array
    {
        return $this->getParaByQuery('layers', $this->id, '$.para[*] ? (@.type.id != "colophon")');
    }
    
    public function getRelatedManuscriptsAttribute(): array
    {
        $relatedManuscripts = DB::table('layers')
            ->selectRaw("jsonb_path_query(jsonb, '$.related_mss[*]') AS related_ms")
            ->where('id', $this->id)
            ->get();
        
        return $relatedManuscripts->map(function ($relatedMs) {
            return json_decode($relatedMs->related_ms, true);
        })->toArray();
    }
    
    private function getAssociatedNamesByQuery(string $jsonPath): array
    {
        $associatedNamesQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query_array(jsonb, ?) AS assoc_names", [$jsonPath])
            ->where('id', $this->id)
            ->first();
        
        if (!$associatedNamesQuery || empty($associatedNamesQuery->assoc_names)) {
            return [];
        }
        
        $associatedNames = json_decode($associatedNamesQuery->assoc_names, true);
        
        $processedNames = array_map(function ($assocName) {
            $agent = isset($assocName['id']) ? Agent::where('ark', $assocName['id'])->first() : null;
            return [
                'id' => $agent ? $agent->id : null,
                'ark' => $agent ? $agent->ark : null,
                'pref_name' => $agent ? $agent->pref_name : null,
                'as_written' => $assocName['as_written'] ?? null,
                'role' => $assocName['role'] ?? null,
                'note' => $assocName['note'] ?? [],
            ];
        }, $associatedNames);
        
        return array_values(array_unique($processedNames, SORT_REGULAR));
    }
    
    public function getAssociatedNamesFromRootAttribute(): array
    {
        return $this->getAssociatedNamesByQuery('$.assoc_name[*]');
    }
    
    private function getAssociatedPlacesByQuery(string $jsonPath): array
    {
        $associatedPlacesQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query_array(jsonb, ?) AS assoc_places", [$jsonPath])
            ->where('id', $this->id)
            ->first();
        
        if (!$associatedPlacesQuery || empty($associatedPlacesQuery->assoc_places)) {
            return [];
        }
        
        $associatedPlaces = json_decode($associatedPlacesQuery->assoc_places, true);
        
        $processedPlaces = array_map(function ($assocPlace) {
            $place = isset($assocPlace['id']) ? Place::where('ark', $assocPlace['id'])->first() : null;
            return [
                'id' => $place ? $place->id : null,
                'ark' => $place ? $place->ark : null,
                'pref_name' => $place ? $place->pref_name : null,
                'as_written' => $assocPlace['as_written'] ?? null,
                'event' => $assocPlace['event'] ?? null,
                'note' => $assocPlace['note'] ?? [],
            ];
        }, $associatedPlaces);
        
        return array_values(array_unique($processedPlaces, SORT_REGULAR));
    }
    
    public function getAssociatedPlacesFromRootAttribute(): array
    {
        return $this->getAssociatedPlacesByQuery('$.assoc_place[*]');
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
    
    public function getReferencesAttribute(): array
    {
        return $this->getReferencesByType('layers', $this->id, 'ref');
    }
    
    public function getBibliographiesAttribute(): array
    {
        return $this->getReferencesByType('layers', $this->id, 'cite');
    }
    
    public function getWorksAttribute(): array
    {
        $textUnits = $this->getTextUnitsAttribute();
        
        return array_values(array_filter(array_merge(
            ...array_map(
                fn($textUnit) => $textUnit['text_unit']['work_wit'] ?? [],
                $textUnits
            )
        )));
    }
    
    public function getAllAssociatedNamesAttribute(): array
    {
        return $this->getRelatedAgents('layers', $this->id, 'strict $.**.assoc_name[*]');
    }
    
    public function getAllAssociatedPlacesAttribute(): array
    {
        return $this->getAssociatedPlacesByQuery('$.**.assoc_place[*]');
    }
    
    public function getReconstructedManuscriptsAttribute(): array
    {
        $manuscripts = DB::table('manuscripts')
            ->select('id', 'ark', 'jsonb')
            ->whereRaw("jsonb_extract_path_text(jsonb, 'reconstruction') = 'true'")
            ->whereRaw("jsonb_path_exists(jsonb, '$.**.layer[*] ? (@.id == \"$this->ark\")')")
            ->get();
        
        return $manuscripts->map(function ($manuscript) {
            $manuscriptData = json_decode($manuscript->jsonb, true);
            
            return [
                'id' => $manuscript->id,
                'ark' => $manuscript->ark,
                'shelfmark' => $manuscriptData['shelfmark'] ?? null,
            ];
        })->toArray();
    }
    
    public function getReconstructedLayersAttribute(): array {
        $layersQuery = DB::table('layers')
            ->select('ark')
            ->whereRaw("jsonb_path_exists(jsonb, '$.reconstructed_from[*] ? (@ == \"$this->ark\")')")
            ->pluck('ark')
            ->toArray();
        
        return $this->getLayersByArks($layersQuery);
    }
    
    public function getReconstructedFromLayersAttribute(): array {
        $layersQuery = DB::table('layers')
            ->selectRaw("jsonb_path_query_array(jsonb, '$.reconstructed_from[*]') AS reconstructed_from")
            ->whereRaw("jsonb_extract_path_text(jsonb, 'reconstruction') = 'true'")
            ->where('id', $this->id)
            ->first();
        
        $reconstructedFromArks = $layersQuery ? json_decode($layersQuery->reconstructed_from, true) : [];
        return $this->getLayersByArks($reconstructedFromArks);
    }
    
    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        unset($array['json']);
        unset($array['jsonb']);

        $data = $this->getJsonData();
        
        // Source field (only manuscripts, not other layers)
        $sourceIds = $this->getSourceIdentifiers();
        if ($sourceIds) {
            $array['source'] = $this->getSourceIdentifiers()['identifier'];
        }
        
        $array['ark'] = $this->ark ?? null;
        $array['identifier'] = $this->identifier ?? null;
        $array['extent'] = $data['extent'] ?? null;
        
        $array['state'] = isset($data['state']['label']) ? $data['state']['label'] : null;
        
        // associated dates
        $array['dates'] = [];
        if (isset($data['assoc_date'])) {
            foreach ($data['assoc_date'] as $date) {
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
        foreach ($data['writing'] as $writing) {
            foreach ($writing['script'] as $script) {
                $array['script'][] = isset($script['label']) ? $script['label'] : null;
            }
        }
        
        // writing system
        $array['writing_system'] = [];
        foreach ($data['writing'] as $writing) {
            foreach ($writing['script'] as $script) {
                $array['writing_system'][] = isset($script['writing_system']) ? $script['writing_system'] : null;
            }
        }
        
        // features
        $array['features'] = [];
        if (isset($data['features'])) {
            foreach ($data['features'] as $feature) {
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
     * @return array|null
     */
    public function getSourceIdentifiers(): ?array
    {
        $data = $this->getJsonData();
        
        if (empty($data['parent'])) {
            return null;
        }
        
        $sourceManuscripts = Manuscript::select('id', 'identifier')
            ->whereIn('ark', $data['parent'])
            ->whereRaw("jsonb_extract_path_text(jsonb, 'reconstruction') = 'false'")
            ->first();
        
        if (!$sourceManuscripts) {
            return null;
        }
        
        return $sourceManuscripts->only(['id', 'identifier']);
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Layer::initialize('layer');