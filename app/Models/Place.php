<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Place extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event',
        'as_written',
        'json',
    ];

    public static $schema;

    public static $uiSchema;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/json/complete/assoc_place.json'));
        self::$uiSchema = File::get(base_path('/schemas/ui/complete/assoc_place.json'));
    }
}

/*
 * Execute the static initializer to load the schema and ui schema.
 */
Place::initialize();

