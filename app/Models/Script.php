<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Script extends Model
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
        'writing_system',
        'when_in_use',
        'region',
        'notes',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data)
    {
        return array_combine($this->fillable, [
            $data['id'],
            $data['label'],
            $data['writing_system'] ?? null,
            $data['when_in_use'] ?? null,
            $data['region'] ?? null,
            $data['notes'] ?? null,
        ]);
    }

    public static $config = [
        'disable_file_uploads' => true,
        'enable_json_forms' => true,
        'index' => [
            'columns' => [
                'id' => 'Id',
                'label' => 'Label',
                'writing_system' => 'Writing system',
                'when_in_use' => 'Dates in use',
                'region' => 'Region',
                'notes' => 'Notes',
            ],
        ],
    ];
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Script::initialize('script');
