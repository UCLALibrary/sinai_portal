<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Inertia\Inertia;

class PlacesController extends Controller
{
    protected $routes = [
        'index' => 'places.index',
        'create' => 'places.create',
        'store' => 'api.places.store',
        'edit' => 'places.edit',
        'update' => 'api.places.update',
        'upload' => [
            'store' => 'api.files.upload.store',
            'update' => 'api.files.upload.update',
            'batch' => 'api.files.upload.batch',
            'resourceType' => 'place',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Places',
            'resources' => Place::paginate(20),
            'columns' => [
                'type' => 'Type',
                'pref_name' => 'Preferred Name'
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
            'title' => 'Create Place',
            'schema' => json_decode(Place::$schema),
            'uischema' => json_decode(Place::$uiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Place',
            'schema' => json_decode(Place::$schema),
            'uischema' => json_decode(Place::$uiSchema),
            'data' => json_decode($place->json),
            'resource' => $place,
            'routes' => $this->routes,
        ]);
    }
}
