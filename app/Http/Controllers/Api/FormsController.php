<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Date;
use App\Models\Part;
use App\Models\Person;
use App\Models\Place;

class FormsController extends Controller
{
    /**
     * Get the schemas for a date resource.
     */
    public function createAssocDate()
    {
        return response()->json([
            'schema' => json_decode(Date::$schema),
            'uischema' => json_decode(Date::$uiSchemaMinimal),
        ]);
    }

    /**
     * Get the schemas for a codicological unit resource.
     */
    public function createCodUnit()
    {
        return response()->json([
            'schema' => json_decode(Part::$schemaMinimal),
            'uischema' => json_decode(Part::$uiSchemaMinimal),
        ]);
    }

    /**
     * Get the schemas for a person resource.
     */
    public function createAssocName()
    {
        return response()->json([
            'schema' => json_decode(Person::$schema),
            'uischema' => json_decode(Person::$uiSchema),
        ]);
    }

    /**
     * Get the schemas for a person resource.
     */
    public function createAssocPlace()
    {
        return response()->json([
            'schema' => json_decode(Place::$schema),
            'uischema' => json_decode(Place::$uiSchema),
        ]);
    }
}
