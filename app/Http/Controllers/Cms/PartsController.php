<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Part;
use Inertia\Inertia;

class PartsController extends Controller
{
    protected $routes = [
        'index' => 'codicological-units.index',
        'create' => 'codicological-units.create',
        'store' => 'api.codicological-units.store',
        'edit' => 'codicological-units.edit',
        'update' => 'api.codicological-units.update',
        'upload' => [
            'store' => 'api.files.upload.store',
            'update' => 'api.files.upload.update',
            'batch' => 'api.files.upload.batch',
            'resourceType' => 'codicologicalUnit',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Codicological Units',
            'resources' => Part::paginate(20),
            'columns' => [
                'identifier' => 'Identifier',
                'ark' => 'ARK'
            ],
            'routes' => $this->routes,
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
            'routes' => $this->routes,
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
                'upload' => 'api.files.upload.update',
            ],
        ]);
    }
}
