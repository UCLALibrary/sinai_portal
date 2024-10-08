<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use App\Traits\HasRelatedEntities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Work extends Model
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
        'pref_title',
        'json',
    ];

    /**
     * The attributes that should be appended to the model.
     *
     * @var array
     */
    protected $appends = ['authors', 'related_works', 'related_agents', 'editions', 'translations', 'citations'];

    /**
     * Accessor to include authors when the model is serialized.
     *
     * @return array
     */
    public function getAuthorsAttribute(): array
    {
        return $this->getRelatedEntities(
            'creator',
            Agent::class,
            function ($item) {
                return isset($item['role']['id']) && $item['role']['id'] === 'author';
            },
            function ($agent, $item) {
                return [
                    'id' => $agent->id,
                    'pref_name' => $agent->pref_name,
                ];
            }
        )->toArray();
    }

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
                    'rel' => $item['rel'] ?? null,
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
     * Get the editions of the work.
     *
     * @return \Illuminate\Support\Collection
     */
    public function editions(): \Illuminate\Support\Collection
    {
        $editionIds = $this->getIdsByType('bib', ['edition']);

        if (!empty($editionIds)) {
            return Reference::whereIn('id', $editionIds)->get();
        }

        return collect([]);
    }

    /**
     * Get the modern translations of the work.
     *
     * @return \Illuminate\Support\Collection
     */
    public function translations(): \Illuminate\Support\Collection
    {
        $translationIds = $this->getIdsByType('bib', ['translation']);

        if (!empty($translationIds)) {
            return Reference::whereIn('id', $translationIds)->get();
        }

        return collect([]);
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
     * Accessor to include editions when the model is serialized.
     *
     * @return array
     */
    public function getEditionsAttribute(): array
    {
        return $this->mapCollection($this->editions(), function ($reference) {
            return [
                'id' => $reference->id,
                'formatted_citation' => $reference->formatted_citation,
                'range' => $this->getRangeForReference($reference->id)
            ];
        });
    }

    /**
     * Accessor to include translations when the model is serialized.
     *
     * @return array
     */
    public function getTranslationsAttribute(): array
    {
        return $this->mapCollection($this->translations(), function ($reference) {
            return [
                'id' => $reference->id,
                'formatted_citation' => $reference->formatted_citation,
                'range' => $this->getRangeForReference($reference->id)
            ];
        });
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
                'range' => $this->getRangeForReference($reference->id)
            ];
        });
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

        // ark
        $array['ark'] = $data['ark'] ?? null;

        // authors
        $authors = collect($this->getAuthorsAttribute())->pluck('pref_name')->all();
        $array['creator'] = !empty($authors) ? $authors : null;

        // incipit
        if (!empty($data['incipit'])) {
            $array['incipit_value'] = isset($data['incipit']['value']) ? $data['incipit']['value'] : null;
            $array['incipit_translation'] = isset($data['incipit']['translation']) ? implode('; ', $data['incipit']['translation']) : null;
        }
        
        // explicit
        if (!empty($data['explicit'])) {
            $array['explicit_value'] = isset($data['explicit']['value']) ? $data['explicit']['value'] : null;
            $array['explicit_translation'] = isset($data['explicit']['translation']) ? implode('; ', $data['explicit']['translation']) : null;
        }
        // genre
        $array['genre'] = [];
        foreach ($data['genre'] as $genre) {
            $array['genre'][] = isset($genre['label']) ? $genre['label'] : null;
        }

        // original language
        $array['orig_lang_label'] = isset($data['orig_lang']['label']) ? $data['orig_lang']['label'] : null;

        // creation date
        if (!empty($data['creation'])) {
            $array['creation_value'] = isset($data['creation']['value']) ? $data['creation']['value'] : null;
            $array['not_before'] = isset($data['creation']['iso']['not_before']) ? $data['creation']['iso']['not_before'] : null;
            $array['not_after'] = isset($data['creation']['iso']['not_after']) ? $data['creation']['iso']['not_after'] : null;
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
Work::initialize('work');
