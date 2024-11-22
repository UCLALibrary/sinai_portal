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
        'colophons'
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
    
    private function getParaByType(string $type = null): array
    {
        $query = '$.para[*]';
        if ($type !== null) {
            $query .= " ? (@.type.id == \"$type\")";
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
