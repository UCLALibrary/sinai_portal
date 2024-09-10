<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reference extends Model
{
    use HasFactory, JsonSchemas;

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

    protected $keyType = 'string';
    public $incrementing = false;

    public static function booted() {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Reference::initialize('reference');