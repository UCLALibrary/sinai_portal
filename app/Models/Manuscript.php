<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Manuscript extends Model
{
    use HasFactory;

    protected $fillable = [
        'ark',
        'shelfmark',
        'json',
    ];

    public static $schema;

    public static $uiSchema;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/json/complete/ms-obj.json'));
        self::$uiSchema = File::get(base_path('/schemas/ui/complete/ms-obj.json'));
    }
}

/*
 * Execute the static initializer to load the schema and ui schema.
 */
Manuscript::initialize();
