<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reference extends Model
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
        'short_title',
        'formatted_citation',
        'zotero_uri',
        'date',
        'creator',
        'category'
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data)
    {
        return array_combine($this->fillable, [
            $data['id'] ?? Str::uuid(),
            $data['short_title'],
            $data['formatted_citation'],
            $data['zotero_uri'] ?? null,
            $data['date'] ?? null,
            $data['creator'] ?? null,
            $data['category'] ?? null,
        ]);
    }

    public static $config = [
        'disable_file_uploads' => true,
        'enable_json_forms' => true,
        'index' => [
            'columns' => [
                'id' => 'Id',
                'short_title' => 'Short Title',
                'formatted_citation' => 'Formatted Citation',
                'zotero_uri' => 'Zotero URI',
                'date' => 'Date',
                'creator' => 'Creator',
                'category' => 'Category',
            ],
        ],
    ];
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Reference::initialize('reference');
