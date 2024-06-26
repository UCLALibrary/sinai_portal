<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Date;

class FormsController extends Controller
{
    public function __construct()
    {
        // execute the static initializer to load the schema and ui schema
        Date::initialize();
    }

    /**
     * Get the schemas for a date resource.
     */
    public function createAssocDate()
    {
        return response()->json([
            'schema' => json_decode(Date::$schema),
            'uischema' => json_decode(Date::$uischema),
        ]);
    }
}
