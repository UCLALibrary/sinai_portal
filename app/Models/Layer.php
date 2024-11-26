<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use App\Traits\RelatedTextUnits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Layer extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities, RelatedTextUnits;
    
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
        'works',
        'all_associated_names',
        'all_associated_places',
        'reconstructed_manuscripts'
    ];
    
    public function getTextUnitsAttribute(): array
    {
        return $this->getRelatedTextUnits('layers', $this->id, '$.text_unit[*]');
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
                'id' => $place ? $place->id : null, // The database ID of the place
                'ark' => $place ? $place->ark : null, // The ARK from the Place table
                'pref_name' => $place ? $place->pref_name : null, // Preferred name from the Place table
                'as_written' => $assocPlace['as_written'] ?? null, // As-written value
                'event' => $assocPlace['event'] ?? null, // Event data
                'note' => $assocPlace['note'] ?? [], // Notes
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
    
    public function getWorksAttribute(): array
    {
        $textUnits = $this->getTextUnitsAttribute();
        $works = [];
        
        foreach ($textUnits as $textUnit) {
            if (isset($textUnit['work_wit']) && is_array($textUnit['work_wit'])) {
                
                foreach ($textUnit['work_wit'] as $workWit) {
                    if (isset($workWit['work']['id'])) {
                        
                        $workArk = $workWit['work']['id'];
                        $work = Work::where('ark', $workArk)->first();
                        
                        if ($work) {
                            $workJson = json_decode($work->json, true);
                            $works[] = [
                                'id' => $work->id,
                                'ark' => $work->ark,
                                'pref_title' => $workJson['pref_title'] ?? null,
                            ];
                        }
                        
                    }
                }
                
            }
        }
        
        return $works;
    }
    
    public function getAllAssociatedNamesAttribute(): array
    {
        return $this->getAssociatedNamesByQuery('$.**.assoc_name[*]');
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
        $array['script'] = [];
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