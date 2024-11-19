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
		'related_agents',
	    /*'related_places',*/
	    'related_overtext_layers',
	    'assoc_dates_overview',
	    'assoc_dates_from_layers',
	    'assoc_places_from_layers',
	    'related_references',
	    'related_bibliographies',
	    'related_digital_versions',
	    'related_text_units'
    ];
    
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
	        SELECT DISTINCT id, jsonb_path_query(jsonb, :jsonPath) AS assoc_date_value
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
			return [
				'id' => $row->id,
				'assoc_date_value' => json_decode($row->assoc_date_value)
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
			
			foreach($layerJson['text_unit'] as $textUnit) {
				$textUnitsArk[] = $textUnit['id'];
			}
		}
		
		$textUnits = TextUnit::whereIn('ark', $textUnitsArk)->get();
		return $textUnits->unique('label')->pluck('label')->toArray();
	}

    public function getConnectedAgentCreatorNames()
    {
        $manuscriptArk = $this->ark;

        $query = "
            WITH manuscript_layers AS (
                SELECT DISTINCT jsonb_array_elements(part -> 'layer') ->> 'id' AS layer_ark
                FROM manuscripts
                CROSS JOIN jsonb_array_elements(jsonb -> 'part') AS part
                WHERE jsonb ->> 'ark' = :manuscriptArk
            ),
            layer_text_units AS (
                SELECT DISTINCT jsonb_array_elements(layer.jsonb -> 'text_unit') ->> 'id' AS text_unit_ark
                FROM layers AS layer
                JOIN manuscript_layers ON layer.jsonb ->> 'ark' = manuscript_layers.layer_ark
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
            SELECT DISTINCT id, agent.jsonb ->> 'pref_name' AS pref_name
            FROM agents AS agent
            JOIN work_agents ON agent.jsonb ->> 'ark' = work_agents.agent_ark;
        ";

        $bindings = [
            'manuscriptArk' => $manuscriptArk,
        ];

        $results = DB::select($query, $bindings);
        return array_map(function ($row) {
          return [
            'id' => $row->id,
            'pref_name' => $row->pref_name,
            'role' => [
              'id' => $row->role_id,
              'label' => $row->role_label
            ],
          ];
        }, $results);
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

        // get all creators attached to this manuscript
        $array['names'] = collect($this->getRelatedAgentsAttribute())->pluck('pref_name');

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
