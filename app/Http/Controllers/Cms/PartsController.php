<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Part;
use Inertia\Inertia;

class PartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Codicological Units',
            'resourceName' => 'codicological-units',
            'resources' => Part::paginate(20),
            'columns' => [
                'identifier' => 'Identifier',
                'ark' => 'ARK'
            ],
            'routes' => [
                'create' => 'codicological-units.create',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Create Codicological Unit',
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uiSchema),
            'routes' => [
                'index' => 'codicological-units.index',
                'store' => 'api.codicological-units.store',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Part $codicologicalUnit)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Codicological Unit',
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uiSchema),
            'data' => json_decode($codicologicalUnit->json),
            'resource' => $codicologicalUnit,
            'routes' => [
                'index' => 'codicological-units.index',
                'update' => 'api.codicological-units.update',
            ],
        ]);
    }
}
