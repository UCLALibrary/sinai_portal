<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasFactory, JsonSchemas;

    public function formContexts(): BelongsToMany
    {
        return $this->belongsToMany(FormContext::class);
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Feature::initialize('feature');