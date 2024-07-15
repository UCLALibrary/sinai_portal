<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Date;

class FormsController extends Controller
{
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
