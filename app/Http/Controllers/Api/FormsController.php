<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Date;
use App\Models\Part;

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
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uiSchemaMinimal),
        ]);
    }
}
