<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bibliography extends Model
{
    use HasFactory, JsonSchemas;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'range',
        'alt_shelf',
        'json',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data, $json)
    {
        return array_combine($this->fillable, [
            $data['type'],
            $data['range'],
            $data['alt_shelf'],
            $json,
        ]);
    }

    public static $config = [
        'index' => [
            'columns' => [
                'type' => 'Type',
                'alt_shelf' => 'Alternative Shelfmark',
                'range' => 'Range',
            ],
        ],
    ];
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Bibliography::initialize('bib');
