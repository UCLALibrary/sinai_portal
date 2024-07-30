<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory, JsonSchemas;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pref_title',
        'json',
    ];
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Work::initialize('work');
