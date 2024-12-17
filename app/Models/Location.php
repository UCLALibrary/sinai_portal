<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
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
        'collection',
        'repository',
        'note',
        'country',
        'address',
        'contact_info',
        'coordinates',
        'url',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data)
    {
        return array_combine($this->fillable, [
            $data['id'],
            $data['collection'],
            $data['repository'],
            $data['note'] ?? null,
            $data['country'] ?? null,
            $data['address'] ?? null,
            $data['contact_info'] ?? null,
            $data['coordinates'] ?? null,
            $data['url'] ?? null,
        ]);
    }

    public static $config = [
        'disable_file_uploads' => true,
        'enable_json_forms' => true,
        'index' => [
            'sort' => [
                'field' => 'collection',
                'direction' => 'asc',
            ],
            'columns' => [
                'id' => 'Id',
                'collection' => 'Collection',
                'repository' => 'Repository',
                'note' => 'Note',
                'country' => 'Country',
            ],
        ],
    ];
}

Location::initialize('location');
