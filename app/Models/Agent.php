<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Agent extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'ark',
        'type',
        'pref_name',
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
            $data['pref_name'],
            $json,
        ]);
    }

    public static $config = [
        'index' => [
            'columns' => [
                'pref_name' => 'Preferred Name',
                'type' => 'Type',
                'ark' => 'ARK',
            ],
        ],
    ];

    /**
     * The attributes that should be appended to the model.
     *
     * @var array
     */
    protected $appends = ['roles', 'assoc_names', 'related_works', 'related_agents', 'citations'];

    /**
     * Accessor to include roles when the model is serialized.
     *
     * @return array
     */
    public function getRolesAttribute()
    {
        $roles = collect()
            ->merge($this->getWorkCreatorRoles())
            ->merge($this->getWorkRelAgentRoles())
            ->merge($this->getLayerAssocNameRoles())
            ->merge($this->getManuscriptAssocNameRoles())
            ->unique()
            ->values()
            ->all();

        return $roles;
    }

    /**
     * Accessor to include assoc names when the model is serialized.
     *
     * @return array
     */
    public function getAssocNamesAttribute()
    {
        $names = array_merge(
            $this->getAssocNamesByJsonPath(Manuscript::class, '$.**.assoc_name[*] ? (@.id == $agent_id)'),
            $this->getAssocNamesByJsonPath(Layer::class, '$.**.assoc_name[*] ? (@.id == $agent_id)')
        );

        $uniqueNames = [];
        foreach ($names as $name) {
            $uniqueNames[$name['id']] = $name;
        }
        
        return $names;
    }

    /**
     * Accessor to include related works when the model is serialized.
     *
     * @return array
     */
    public function getRelatedWorksAttribute(): array
    {
        $agentArk = $this->ark;

        $query = "
            WITH matching_creators AS (
                SELECT
                    work.id,
                    work.jsonb ->> 'pref_title' AS pref_title,
                    work.jsonb ->> 'ark' AS work_ark,
                    creator_elem ->> 'id' AS agent_ark,
                    creator_elem ->> 'role' AS role_label
                FROM works AS work
                JOIN LATERAL jsonb_array_elements(work.jsonb -> 'creator') AS creator_elem
                    ON TRUE
                WHERE creator_elem ->> 'id' = ?
            ),
            matching_rel_agents AS (
                SELECT
                    work.id,
                    work.jsonb ->> 'pref_title' AS pref_title,
                    work.jsonb ->> 'ark' AS work_ark,
                    rel_agent_elem ->> 'id' AS agent_ark,
                    rel_agent_elem ->> 'rel' AS role_label
                FROM works AS work
                JOIN LATERAL jsonb_array_elements(work.jsonb -> 'rel_agent') AS rel_agent_elem
                    ON TRUE
                WHERE rel_agent_elem ->> 'id' = ?
            )
            SELECT id, pref_title, work_ark, role_label
            FROM matching_creators
            UNION ALL
            SELECT id, pref_title, work_ark, role_label
            FROM matching_rel_agents;
        ";

        $bindings = [
            $agentArk, // for the first '?'
            $agentArk, // for the second '?'
        ];

        $results = DB::select($query, $bindings);

        $worksWithRoles = array_map(function ($row) {
            return [
                'id' => $row->id,
                'pref_title' => $row->pref_title,
                'work_ark' => $row->work_ark,
                'roles' => json_decode($row->role_label, true),
            ];
        }, $results);

        return $worksWithRoles;

    }

    /**
     * Accessor to include related agents when the model is serialized.
     *
     * @return array
     */
    public function getRelatedAgentsAttribute(): array
    {
        return $this->getRelatedEntities(
        'rel_agent',
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
     * Accessor to include citations when the model is serialized.
     *
     * @return array
     */
    public function getCitationsAttribute(): array
    {
        return $this->mapCollection($this->citations(), function ($reference) {
            return [
                'id' => $reference->id,
                'formatted_citation' => $reference->formatted_citation,
                'range' => $this->getRangeForReference($reference->id),
            ];
        });
    }

    /**
     * Get the references (citations) of the work.
     *
     * @return \Illuminate\Support\Collection
     */
    public function citations(): \Illuminate\Support\Collection
    {
        $citationIds = $this->getIdsByType('bib', ['cite', 'ref']);

        if (!empty($citationIds)) {
            return Reference::whereIn('id', $citationIds)->get();
        }

        return collect([]);
    }
   
    protected function getWorkCreatorRoles()
    {
        return $this->getRolesByJsonPath(Work::class, '$.**.creator[*] ? (@.id == $agent_id).role.label');
    }

    protected function getWorkRelAgentRoles()
    {
        return $this->getRolesByJsonPath(Work::class, '$.**.rel_agent[*] ? (@.id == $agent_id).rel[*].label');
    }

    protected function getLayerAssocNameRoles()
    {
        return $this->getRolesByJsonPath(Layer::class, '$.**.assoc_name[*] ? (@.id == $agent_id).role.label');
    }

    protected function getManuscriptAssocNameRoles()
    {
        return $this->getRolesByJsonPath(Manuscript::class, '$.**.assoc_name[*] ? (@.id == $agent_id).role.label');
    }

    protected function getRolesByJsonPath($modelClass, $jsonPath)
    {
        $agentArk = $this->ark;
        $tableName = (new $modelClass)->getTable();

        $query = "
            SELECT DISTINCT role_label.value AS role
            FROM {$tableName},
            LATERAL jsonb_path_query({$tableName}.jsonb, :jsonPath, :vars) AS role_label(value)
            WHERE jsonb_path_exists({$tableName}.jsonb, :existsJsonPath, :vars)
        ";

        $bindings = [
            'jsonPath' => $jsonPath,
            'existsJsonPath' => $jsonPath, // Use the same path for existence check
            'vars' => json_encode(['agent_id' => $agentArk]),
        ];

        $roles = DB::select($query, $bindings);

        $rolesArray = array_map(function($row) {
            return json_decode($row->role, true);
        }, $roles);

        return collect($rolesArray)->flatten()->unique()->values()->all();
    }

    protected function getAssocNamesByJsonPath($modelClass, $jsonPath)
    {
        $agentArk = $this->ark;
        $tableName = (new $modelClass)->getTable();

        $query = "
            SELECT DISTINCT assoc_name
            FROM {$tableName},
            LATERAL jsonb_path_query({$tableName}.jsonb, :jsonPath, :vars) AS assoc_name
            WHERE jsonb_path_exists({$tableName}.jsonb, :existsJsonPath, :vars)
        ";

        $bindings = [
            'jsonPath' => $jsonPath,
            'existsJsonPath' => $jsonPath, // Use the same path for existence check
            'vars' => json_encode(['agent_id' => $agentArk]),
        ];

        // decode the JSON string for each assoc name to return an array of assoc names
        $assocNamesJson = DB::select($query, $bindings);

        $assocNames = array_map(function($row) {
            return json_decode($row->assoc_name, true);
        }, $assocNamesJson);

        return $assocNames;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $data = $this->getJsonData();

        // ark
        $array['ark'] = $data['ark'] ?? null;

        // gender
        $array['gender'] = isset($data['gender']['label']) ? $data['gender']['label'] : null;

        // birth
        $birth = $data['birth'] ?? null;
        if ($birth) {
            $array['birth_value'] = $birth['value'] ? 'b. ' . $birth['value'] : null;
            $array['birth_not_before'] = isset($birth['iso']['not_before']) ? $birth['iso']['not_before'] : null;
            $array['birth_not_after'] = isset($birth['iso']['not_after']) ? $birth['iso']['not_after'] : null;
        }

        // death
        $death = $data['death'] ?? null;
        if ($death) {
            $array['death_value'] = $death['value'] ? 'd. ' . $death['value'] : null;
            $array['death_not_before'] = isset($death['iso']['not_before']) ? $death['iso']['not_before'] : null;
            $array['death_not_after'] = isset($death['iso']['not_after']) ? $death['iso']['not_after'] : null;
        }

        // floruit
        $floruit = $data['floruit'] ?? null;
        if ($floruit) {
            $array['floruit_value'] = $floruit['value'] ? 'fl. ' . $floruit['value'] : null;
            $array['floruit_not_before'] = isset($floruit['iso']['not_before']) ? $floruit['iso']['not_before'] : null;
            $array['floruit_not_after'] = isset($floruit['iso']['not_after']) ? $floruit['iso']['not_after'] : null;
        }

        // minimum date from birth, death, and floruit 'not_before' fields
        $values = array_filter([
            $array['birth_not_before'] ?? null,
            $array['death_not_before'] ?? null,
            $array['floruit_not_before'] ?? null
        ], fn($value) => $value !== null);
        $array['date_min'] = $values ? min(array_map('intval', $values)) : null;

        // maximum date from birth, death, and floruit 'not_after' fields
        $values = array_filter([
            $array['birth_not_after'] ?? null,
            $array['death_not_after'] ?? null,
            $array['floruit_not_after'] ?? null
        ], fn($value) => $value !== null);
        $array['date_max'] = $values ? max(array_map('intval', $values)) : null;

        // roles
        $array['roles'] = $this->getRolesAttribute();

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
Agent::initialize('agent');
