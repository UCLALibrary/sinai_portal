<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Agent extends Model
{
    use HasFactory, Searchable, HasRelatedEntities;

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
     * The attributes that should be appended to the model.
     *
     * @var array
     */
    protected $appends = ['related_works', 'related_agents', 'citations'];

    /**
     * Accessor to include related works when the model is serialized.
     *
     * @return array
     */
    public function getRelatedWorksAttribute(): array
    {
        return $this->getRelatedEntities(
            'rel_work',
            Work::class,
            null,
            function ($work, $item) {
                return [
                    'id' => $work->id,
                    'pref_title' => $work->pref_title,
                ];
            }
        )->toArray();
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
        $array['gender'] = $data['gender'] ?? null;

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

        /*
         * Apply default transformations if desired.
         * 
         * https://www.algolia.com/doc/framework-integration/laravel/indexing/configure-searchable-data/?client=php#transformers
         */
        // $array = $this->transform($array);

        return $array;
    }
}