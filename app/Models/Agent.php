<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Agent extends Model
{
    use HasFactory, JsonSchemas, Searchable;

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
        'type',
        'pref_name',
        'json',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // decode json field
        $data = json_decode($this->json, true);

        // ark
        $array['ark'] = $data['ark'] ?? null;

        // gender
        $array['gender'] = $data['gender'] ?? null;

        // birth
        $birth = $data['birth'] ?? null;
        if ($birth) {
            $array['birth_value'] = $birth['value'] ? 'b. ' . $birth['value'] : null;
            $array['birth_not_before'] = $birth['iso']['not_before'] ?? null;
            $array['birth_not_after'] = $birth['iso']['not_after'] ?? null;
        }

        // death
        $death = $data['death'] ?? null;
        if ($death) {
            $array['death_value'] = $death['value'] ? 'd. ' . $death['value'] : null;
            $array['death_not_before'] = $death['iso']['not_before'] ?? null;
            $array['death_not_after'] = $death['iso']['not_after'] ?? null;
        }

        // floruit
        $floruit = $data['floruit'] ?? null;
        if ($floruit) {
            $array['floruit_value'] = $floruit['value'] ? 'fl. ' . $floruit['value'] : null;
            $array['floruit_not_before'] = $floruit['iso']['not_before'] ?? null;
            $array['floruit_not_after'] = $floruit['iso']['not_after'] ?? null;
        }

        // minimum not_before
        $values = array_filter([
            $array['birth_not_before'] ?? null,
            $array['death_not_before'] ?? null,
            $array['floruit_not_before'] ?? null
        ], fn($value) => $value !== null);
        $array['not_before_min'] = $values ? min(array_map('intval', $values)) : null;

        // maximum not_after
        $values = array_filter([
            $array['birth_not_after'] ?? null,
            $array['death_not_after'] ?? null,
            $array['floruit_not_after'] ?? null
        ], fn($value) => $value !== null);
        $array['not_after_max'] = $values ? max(array_map('intval', $values)) : null;

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
