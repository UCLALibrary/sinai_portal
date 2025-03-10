<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory, JsonSchemas;

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
	
	protected $keyType = 'string';
	public $incrementing = false;

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data, $json)
    {
        return array_combine($this->fillable, [
            basename($data['ark']),  // use the trailing ark segment as the id
            $data['type'],
            $data['pref_name'],
            $json,
        ]);
    }

    public static $config = [
        'index' => [
            'sort' => [
                'field' => 'pref_name',
                'direction' => 'asc',
            ],
            'columns' => [
                'pref_name' => 'Preferred Name',
                'type' => 'Type',
                'ark' => 'ARK',
            ],
        ],
    ];
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Place::initialize('place');
