<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Bibliography;
use Inertia\Inertia;

class BibliographyController extends Controller
{
    protected $routes = [
        'index' => 'bibliography.index',
        'create' => 'bibliography.create',
        'store' => 'api.bibliography.store',
        'edit' => 'bibliography.edit',
        'update' => 'api.bibliography.update',
        'upload' => [
            'store' => 'api.files.upload.store',
            'update' => 'api.files.upload.update',
            'batch' => 'api.files.upload.batch',
            'resourceType' => 'bibliography',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Bibliography',
            'resources' => Bibliography::paginate(20),
            'columns' => [
                'type' => 'Type',
                'alt_shelf' => 'Alternative Shelfmark',
                'range' => 'Range',
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
            'title' => 'Create Bibliography',
            'schema' => json_decode(Bibliography::$schema),
            'uischema' => json_decode(Bibliography::$uiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bibliography $bibliography)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Bibliography',
            'schema' => json_decode(Bibliography::$schema),
            'uischema' => json_decode(Bibliography::$uiSchema),
            'data' => json_decode($bibliography->json),
            'resource' => $bibliography,
            'routes' => $this->routes,
        ]);
    }
}
