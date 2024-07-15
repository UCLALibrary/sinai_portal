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

    public static $uischema;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/assoc_date.json'));
        self::$uischema = File::get(base_path('/schemas/ui/assoc_date.json'));
    }
}

/*
 * Execute the static initializer to load the schema and ui schema.
 */
Date::initialize();
