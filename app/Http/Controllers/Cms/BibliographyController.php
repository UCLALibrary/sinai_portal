<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Bibliography;
use Inertia\Inertia;

class BibliographyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Bibliography',
            'resourceName' => 'bibliography',
            'resources' => Bibliography::paginate(20),
            'columns' => [
                'type' => 'Type',
                'alt_shelf' => 'Alternative Shelfmark',
                'range' => 'Range',
            ],
            'routes' => [
                'create' => 'bibliography.create',
            ],
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
            'routes' => [
                'index' => 'bibliography.index',
                'store' => 'api.bibliography.store',
            ],
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
            'routes' => [
                'index' => 'bibliography.index',
                'update' => 'api.bibliography.update',
            ],
        ]);
    }
}
