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

    protected $appends = ['authors'];

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

        // authors
        $authors = [];

        $creators = $data['creator'] ?? [];
        $authorIds = [];

        foreach ($creators as $creator) {
            if ($creator['role'] === 'author' && isset($creator['id'])) {
                $arkParts = explode('/', $creator['id']);
                $authorIds[] = end($arkParts);
            }
        }

        if (!empty($authorIds)) {
            $agents = Agent::whereIn('id', $authorIds)->get();
            foreach ($agents as $agent) {
                $authors[] = $agent->pref_name;
            }
        }

        $array['creator'] = count($authors) > 0 ? $authors : null;

        // incipit
        $incipit = $data['incipit'] ?? null;
        if ($incipit) {
            $array['incipit_value'] = $incipit['value'] ?? null;
            $array['incipit_translation'] = implode('; ', $incipit['translation']) ?? null;
        }
        
        // explicit
        $explicit = $data['explicit'] ?? null;
        if ($explicit) {
            $array['explicit_value'] = $explicit['value'] ?? null;
            $array['explicit_translation'] = implode('; ', $explicit['translation']) ?? null;
        }

        // genre
        $array['genre'] = $data['genre'] ?? null;

        // original language
        $array['orig_lang_label'] = $data['orig_lang']['label'] ?? null;

        // creation date
        $creationDate = $data['creation'] ?? null;
        if ($creationDate) {
            $array['creation_value'] = $creationDate['value'] ?? null;
            $array['not_before'] = $creationDate['iso']['not_before'] ?? null;
            $array['not_after'] = $creationDate['iso']['not_after'] ?? null;
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
     * Get the authors of the work.
     *
     * @return \Illuminate\Support\Collection
     */
    public function authors()
    {
        $data = json_decode($this->json, true);

        $creators = $data['creator'] ?? [];
        $authorIds = [];

        foreach ($creators as $creator) {
            if ($creator['role'] === 'author' && isset($creator['id'])) {
                // Extract the last part of the ARK identifier
                $arkParts = explode('/', $creator['id']);
                $authorId = end($arkParts);

                if ($authorId) {
                    $authorIds[] = $authorId;
                }
            }
        }

        if (!empty($authorIds)) {
            // Fetch authors from the agents table
            return Agent::whereIn('id', $authorIds)->get();
        }

        return collect([]);
    }

    public function getAuthorsAttribute()
    {
        return $this->authors()->map(function ($author) {
            return [
                'id' => $author->id,
                'pref_name' => $author->pref_name,
            ];
        });
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Work::initialize('work');
