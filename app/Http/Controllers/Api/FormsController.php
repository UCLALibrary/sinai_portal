<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bibliography;
use App\Models\Part;
use App\Models\Person;
use App\Models\Place;
use Illuminate\Http\JsonResponse;

class FormsController extends Controller
{
    /**
     * Get the schemas for a codicological unit resource.
     */
    public function codUnit(Part $codicologicalUnit = null): JsonResponse
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
    public function createAssocName(): JsonResponse
    {
        return response()->json([
            'schema' => json_decode(Person::$schema),
            'uischema' => json_decode(Person::$uiSchema),
        ]);
    }

    /**
     * Get the schemas for a person resource.
     */
    public function createAssocPlace(): JsonResponse
    {
        return response()->json([
            'schema' => json_decode(Place::$schema),
            'uischema' => json_decode(Place::$uiSchema),
        ]);
    }

    /**
     * Get the schemas for a bibliography resource.
     */
    public function createBib(): JsonResponse
    {
        return response()->json([
            'schema' => json_decode(Bibliography::$schema),
            'uischema' => json_decode(Bibliography::$uiSchema),
        ]);
    }
}
