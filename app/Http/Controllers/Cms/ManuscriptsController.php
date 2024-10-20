<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use Inertia\Inertia;

class ManuscriptsController extends Controller
{
    protected $routes = [
        'index' => 'manuscripts.index',
        'create' => 'manuscripts.create',
        'store' => 'api.manuscripts.store',
        'edit' => 'manuscripts.edit',
        'update' => 'api.manuscripts.update',
        'upload' => [
            'store' => 'api.files.upload.store',
            'update' => 'api.files.upload.update',
            'batch' => 'api.files.upload.batch',
            'resourceType' => 'manuscript',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Manuscripts',
            'resources' => Manuscript::paginate(20),
            'columns' => [
                'ark' => 'ARK',
                'identifier' => 'Identifier'
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
            'title' => 'Create Manuscript',
            'schema' => json_decode(Manuscript::$schema),
            'uischema' => json_decode(Manuscript::$uiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manuscript $manuscript)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Manuscript',
            'schema' => json_decode(Manuscript::$schema),
            'uischema' => json_decode(Manuscript::$uiSchema),
            'data' => json_decode($manuscript->json),
            'resource' => $manuscript,
            'routes' => $this->routes,
        ]);
    }
}
