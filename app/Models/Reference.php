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
            $data['short_title'],
            $data['formatted_citation'],
            $data['zotero_uri'],
            $data['date'],
            $data['creator'],
            $data['category'],
        ]);
    }

    public static function booted() {
        static::creating(function ($model) {
            if(!isset($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Reference::initialize('reference');
