<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasFactory, JsonSchemas;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'term',
        'corresp_note',
        'summary',
        'scope',
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