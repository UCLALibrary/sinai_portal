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
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Work::initialize('work');
