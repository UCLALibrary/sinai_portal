<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bibliography;
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
            'uischema' => json_decode(Date::$uiSchema),
        ]);
    }

    /**
     * Get the schemas for a codicological unit resource.
     */
    public function codUnit(Part $codicologicalUnit = null)
    {
        return response()->json([
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uiSchema),
            'data' => $codicologicalUnit ? json_decode($codicologicalUnit->json) : null,
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

    /**
     * Get the schemas for a bibliography resource.
     */
    public function createBib()
    {
        return response()->json([
            'schema' => json_decode(Bibliography::$schema),
            'uischema' => json_decode(Bibliography::$uiSchema),
        ]);
    }
}
