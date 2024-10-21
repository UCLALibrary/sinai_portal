<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory, JsonSchemas;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'ark',
        'json',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data, $json)
    {
        return array_combine($this->fillable, [
            basename($data['ark']),  // use the trailing ark segment as the id
            $data['ark'],
            $json,
        ]);
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Content::initialize('content_unit');
