<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
	
	protected $appends = ['related_agents'];
	
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
					'pref_name' => $agent->pref_name,
					'rel' => $item['rel'] ?? null,
				];
			})->toArray();
	}

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
        foreach($data['part'] as $part) {
            foreach($part['support'] as $support) {
                $array['support'][] = isset($support['label']) ? $support['label'] : null;
            }
        }

        // features
        $array['features'] = [];
        if (isset($data['features'])) {
            foreach($data['features'] as $feature) {
                $array['features'][] = isset($feature['label']) ? $feature['label'] : null;
            }
        }
        
        // location (repository, collection)
        $array['repository'] = [];
        $array['collection'] = [];
        if (isset($data['location'])) {
            foreach($data['location'] as $location) {
                $array['repository'] = isset($location['repository']) ? $location['repository'] : null;
                $array['collection'] = isset($location['collection']) ? $location['collection'] : null;
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
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Manuscript::initialize('ms-obj');
