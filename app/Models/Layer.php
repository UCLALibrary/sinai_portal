<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
            }
        }

        // script
        $array['script'] = [];
        foreach($data['writing'] as $writing) {
            foreach($writing['script'] as $script) {
                $array['script'][] = isset($script['label']) ? $script['label'] : null;
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
Manuscript::initialize('ms-obj');