<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait JsonSchemas
{
    public static $schema;

    public static $uiSchema;

    public static function initialize($schemaName)
    {
        $schemaPath = base_path('/schemas/json/' . $schemaName . '.json');
        self::$schema = File::exists($schemaPath) ? File::get($schemaPath) : null;

        $uiSchemaPath = base_path('/schemas/ui/' . $schemaName . '.json');
        self::$uiSchema = File::exists($uiSchemaPath) ? File::get($uiSchemaPath) : null;
    }
}
