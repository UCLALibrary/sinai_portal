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
        'type',
        'pref_name',
        'json',
    ];
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Place::initialize('place');
