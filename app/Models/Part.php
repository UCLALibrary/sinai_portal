<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Part extends Model
{
    use HasFactory;

    protected $fillable = ['json'];

    public static $schema;

    public static $uiSchema;

    public static $uiSchemaMinimal;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/json/copmlete/cod_unit.json'));
        self::$uiSchema = File::get(base_path('/schemas/ui/complete/cod_unit.json'));
        self::$uiSchemaMinimal = File::get(base_path('/schemas/ui/minimal/cod_unit.json'));
    }
}

/*
 * Execute the static initializer to load the schema and ui schema.
 */
Part::initialize();
