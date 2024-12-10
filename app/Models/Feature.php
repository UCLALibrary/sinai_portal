<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Feature extends Model
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
        'corresp_note',
        'summary',
        'scope',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data)
    {
        return array_combine($this->fillable, [
            $data['id'] ?? Str::slug($data['label'], '-'),
            $data['label'],
            $data['corresp_note'] ?? null,
            $data['summary'] ?? null,
            $data['scope'] ?? null,
        ]);
    }

    public static $config = [
        'disable_file_uploads' => true,
        'enable_json_forms' => true,
        'index' => [
            'columns' => [
                'id' => 'Id',
                'label' => 'Label',
                'corresp_note' => 'Note',
                'summary' => 'Summary',
                'scope' => 'Scope',
            ],
        ],
    ];

    public function formContexts(): BelongsToMany
    {
        return $this->belongsToMany(FormContext::class);
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Feature::initialize('feature');
