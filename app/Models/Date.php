<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Date extends Model
{
    use HasFactory;

    protected $fillable = ['json'];

    public static $schema;

    public static $uiSchema;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/json/complete/assoc_date.json'));
        self::$uiSchema = File::get(base_path('/schemas/ui/complete/assoc_date.json'));
    }
}

/*
 * Execute the static initializer to load the schema and ui schema.
 */
Date::initialize();
