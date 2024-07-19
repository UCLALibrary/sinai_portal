<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait JsonSchemas
{
    public static $schema;

    public static $uiSchema;

    public static $schemaMinimal;

    public static $uiSchemaMinimal;

    public static function initialize($schemaName)
    {
        $schemaPath = base_path('/schemas/json/complete/' . $schemaName . '.json');
        self::$schema = File::exists($schemaPath) ? File::get($schemaPath) : null;

        $uiSchemaPath = base_path('/schemas/ui/complete/' . $schemaName . '.json');
        self::$uiSchema = File::exists($uiSchemaPath) ? File::get($uiSchemaPath) : null;

        $schemaMinimal = base_path('/schemas/json/minimal/' . $schemaName . '.json');
        self::$schemaMinimal = File::exists($schemaMinimal) ? File::get($schemaMinimal) : null;

        $uiSchemaMinimal = base_path('/schemas/ui/minimal/' . $schemaName . '.json');
        self::$uiSchemaMinimal = File::exists($uiSchemaMinimal) ? File::get($uiSchemaMinimal) : null;
    }
}
