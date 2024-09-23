<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Work extends Model
{
    use HasFactory, JsonSchemas, Searchable;

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
     * Cached decoded JSON data.
     *
     * @var array|null
     */
    protected $jsonData = null;

    /**
     * Get the decoded JSON data.
     *
     * @return array
     */
    protected function getJsonData(): array
    {
        if ($this->jsonData === null) {
            $this->jsonData = json_decode($this->json, true);
        }
        return $this->jsonData;
    }

    /**
     * Extract the last part of an ARK identifier to get the ID.
     *
     * @param string $arkIdentifier
     * @return string|null
     */
    protected function extractIdFromArk(string $arkIdentifier): string|null
    {
        if ($arkIdentifier) {
            $arkParts = explode('/', $arkIdentifier);
            return end($arkParts);
        }
        return null;
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
        $authors = $this->authors()->pluck('pref_name')->all();
        $array['creator'] = !empty($authors) ? $authors : null;

        // incipit
        if (!empty($data['incipit'])) {
            $array['incipit_value'] = $data['incipit']['value'] ?? null;
            $array['incipit_translation'] = isset($data['incipit']['translation']) ? implode('; ', $data['incipit']['translation']) : null;
        }
        
        // explicit
        if (!empty($data['explicit'])) {
            $array['explicit_value'] = $data['explicit']['value'] ?? null;
            $array['explicit_translation'] = isset($data['explicit']['translation']) ? implode('; ', $data['explicit']['translation']) : null;
        }

        // genre
        $array['genre'] = $data['genre'] ?? null;

        // original language
        $array['orig_lang_label'] = $data['orig_lang']['label'] ?? null;

        // creation date
        if (!empty($data['creation'])) {
            $array['creation_value'] = $data['creation']['value'] ?? null;
            $array['not_before'] = $data['creation']['iso']['not_before'] ?? null;
            $array['not_after'] = $data['creation']['iso']['not_after'] ?? null;
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
     * Extract author IDs from the JSON data.
     *
     * @return array
     */
    private function getAuthorIds(): array
    {
        $data = $this->getJsonData();

        $creators = $data['creator'] ?? [];
        $authorIds = [];

        foreach ($creators as $creator) {
            if ($creator['role'] === 'author' && isset($creator['id'])) {
                $authorId = $this->extractIdFromArk($creator['id']);

                if ($authorId) {
                    $authorIds[] = $authorId;
                }
            }
        }

        return $authorIds;
    }

    /**
     * Get the authors of the work.
     *
     * @return \Illuminate\Support\Collection
     */
    public function authors(): \Illuminate\Support\Collection
    {
        $authorIds = $this->getAuthorIds();

        if (!empty($authorIds)) {
            // Fetch authors from the agents table
            return Agent::whereIn('id', $authorIds)->get();
        }

        return collect([]);
    }

    /**
     * Accessor to include authors when the model is serialized.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAuthorsAttribute(): \Illuminate\Support\Collection
    {
        return $this->authors()->map(function ($author) {
            return [
                'id' => $author->id,
                'pref_name' => $author->pref_name,
            ];
        });
    }

    /**
     * Extract related work IDs from the JSON data.
     *
     * @return array
     */
    private function getRelatedWorkIds(): array
    {
        $data = $this->getJsonData();

        $relatedWorks = $data['rel_work'] ?? [];
        $relatedWorkIds = [];

        foreach ($relatedWorks as $relatedWork) {
            if (isset($relatedWork['id'])) {
                $relatedWorkId = $this->extractIdFromArk($relatedWork['id']);

                if ($relatedWorkId) {
                    $relatedWorkIds[] = $relatedWorkId;
                }
            }
        }

        return $relatedWorkIds;
    }

    public function relatedWorks(): \Illuminate\Support\Collection
    {
        $relatedWorkIds = $this->getRelatedWorkIds();

        if (!empty($relatedWorkIds)) {
            // Fetch related works from the works table
            return self::whereIn('id', $relatedWorkIds)->get();
        }

        return collect([]);
    }

    /**
     * Accessor to include related works when the model is serialized.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRelatedWorksAttribute(): \Illuminate\Support\Collection
    {
        return $this->relatedWorks()->map(function ($work) {
            return [
                'id' => $work->id,
                'pref_title' => $work->pref_title,
            ];
        });
    }

    private function getRelatedAgentIds(): array
    {
        $data = $this->getJsonData();

        $relatedAgents = $data['rel_agent'] ?? [];
        $relatedAgentIds = [];
        $relatedAgentRels = [];

        foreach ($relatedAgents as $relatedAgent) {
            if (isset($relatedAgent['id'])) {
                $relatedAgentId = $this->extractIdFromArk($relatedAgent['id']);

                if ($relatedAgentId) {
                    $relatedAgentIds[] = $relatedAgentId;
                    $relatedAgentRels[$relatedAgentId] = $relatedAgent['rel'] ?? null;
                }
            }
        }

        return ['ids' => $relatedAgentIds, 'rels' => $relatedAgentRels];
    }

    public function relatedAgents(): \Illuminate\Support\Collection
    {
        $relatedAgentData = $this->getRelatedAgentIds();
        $relatedAgentIds = $relatedAgentData['ids'];
        $relatedAgentRels = $relatedAgentData['rels'];

        if (!empty($relatedAgentIds)) {
            // Fetch all related agents in a single query
            $agents = Agent::whereIn('id', $relatedAgentIds)->get();

            // Map the agents with their relationship types
            return $agents->map(function ($agent) use ($relatedAgentRels) {
                return [
                    'id' => $agent->id,
                    'pref_name' => $agent->pref_name,
                    'rel' => $relatedAgentRels[$agent->id] ?? null,
                ];
            });
        }

        return collect([]);
    }

    /**
     * Accessor to include related agents when the model is serialized.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRelatedAgentsAttribute(): \Illuminate\Support\Collection
    {
        return $this->relatedAgents();
    }

    /**
     * Extract bib IDs from the JSON data based on type IDs.
     *
     * @param array $typeIds
     * @return array
     */
    private function getBibIdsByType(array $typeIds): array
    {
        $data = $this->getJsonData();

        $bibEntries = $data['bib'] ?? [];
        $bibIds = [];

        foreach ($bibEntries as $bibEntry) {
            if (isset($bibEntry['id'], $bibEntry['type']['id'])) {
                $bibTypeId = $bibEntry['type']['id'];
                if (in_array($bibTypeId, $typeIds)) {
                    $bibIds[] = $bibEntry['id'];
                }
            }
        }

        return $bibIds;
    }

    /**
     * Get the editions of the work.
     *
     * @return \Illuminate\Support\Collection
     */
    public function editions(): \Illuminate\Support\Collection
    {
        $editionIds = $this->getBibIdsByType(['edition']);

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
        $translationIds = $this->getBibIdsByType(['translation']);

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
        $citationIds = $this->getBibIdsByType(['cite', 'ref']);

        if (!empty($citationIds)) {
            return Reference::whereIn('id', $citationIds)->get();
        }

        return collect([]);
    }

    /**
     * Accessor to include editions when the model is serialized.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEditionsAttribute(): \Illuminate\Support\Collection
    {
        return $this->editions()->map(function ($reference) {
            return [
                'id' => $reference->id,
                'formatted_citation' => $reference->formatted_citation,
            ];
        });
    }

    /**
     * Accessor to include translations when the model is serialized.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTranslationsAttribute(): \Illuminate\Support\Collection
    {
        return $this->translations()->map(function ($reference) {
            return [
                'id' => $reference->id,
                'formatted_citation' => $reference->formatted_citation,
            ];
        });
    }

    /**
     * Accessor to include citations when the model is serialized.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCitationsAttribute(): \Illuminate\Support\Collection
    {
        return $this->citations()->map(function ($reference) {
            return [
                'id' => $reference->id,
                'formatted_citation' => $reference->formatted_citation,
            ];
        });
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Work::initialize('work');
