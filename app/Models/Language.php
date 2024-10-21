<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory, JsonSchemas;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'label',
        'iso',
        'glottolog',
        'writing_systems',
        'other_names',
        'when_in_use',
        'regions',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data)
    {
        return array_combine($this->fillable, [
            $data['id'],
            $data['label'],
            $data['iso'],
            $data['glottolog'],
            $data['writing_systems'],
            $data['other_names'],
            $data['when_in_use'],
            $data['regions'],
        ]);
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Language::initialize('language');
