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
            $data['note'],
            $data['country'],
            $data['address'],
            $data['contact_info'],
            $data['coordinates'],
            $data['url'],
        ]);
    }
}

Location::initialize('location');
