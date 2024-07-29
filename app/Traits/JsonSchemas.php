<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait JsonSchemas
{
    public static $schema;
    public static $uiSchema;

    public static $createSchema;
    public static $createUiSchema;

    public static $editSchema;
    public static $editUiSchema;

    public static function initialize($schemaName)
    {
        $schemaPath = base_path('/schemas/json/' . $schemaName . '.json');
        self::$schema = File::exists($schemaPath) ? File::get($schemaPath) : null;

        $uiSchemaPath = base_path('/schemas/ui/' . $schemaName . '.json');
        self::$uiSchema = File::exists($uiSchemaPath) ? File::get($uiSchemaPath) : null;

        $createSchemaPath = base_path('/schemas/json/' . $schemaName . '.create.json');
        self::$createSchema = File::exists($createSchemaPath) ? File::get($createSchemaPath) : null;

        $createUiSchemaPath = base_path('/schemas/ui/' . $schemaName . '.create.json');
        self::$createUiSchema = File::exists($createUiSchemaPath) ? File::get($createUiSchemaPath) : null;

        $editSchemaPath = base_path('/schemas/json/' . $schemaName . '.edit.json');
        self::$editSchema = File::exists($editSchemaPath) ? File::get($editSchemaPath) : null;

        $editUiSchemaPath = base_path('/schemas/ui/' . $schemaName . '.edit.json');
        self::$editUiSchema = File::exists($editUiSchemaPath) ? File::get($editUiSchemaPath) : null;
    }
}
