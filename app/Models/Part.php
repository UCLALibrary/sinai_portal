<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Part extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ark',
        'identifier',
        'json',
    ];

    public static $schema;

    public static $uiSchema;

    public static $schemaMinimal;

    public static $uiSchemaMinimal;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/json/complete/cod_unit.json'));
        self::$uiSchema = File::get(base_path('/schemas/ui/complete/cod_unit.json'));
        self::$schemaMinimal = File::get(base_path('/schemas/json/minimal/cod_unit.json'));
        self::$uiSchemaMinimal = File::get(base_path('/schemas/ui/minimal/cod_unit.json'));
    }
}

/*
 * Execute the static initializer to load the schema and ui schema.
 */
Part::initialize();
