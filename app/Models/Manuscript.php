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

    protected $appends = ['related_agents', 'related_references', 'related_bibliographies', 'related_digital_versions'];
    
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
