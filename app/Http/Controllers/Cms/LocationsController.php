<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Inertia\Inertia;

class LocationsController extends Controller
{
    protected $routes = [
        'index' => 'locations.index',
        'create' => 'locations.create',
        'store' => 'api.locations.store',
        'edit' => 'locations.edit',
        'update' => 'api.locations.update',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Locations',
            'resources' => Location::paginate(20),
            'columns' => [
                'id' => 'ID',
                'collection' => 'Collection',
                'repository' => 'Repository',
                'note' => 'Note',
                'country' => 'Country'
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
            'title' => 'Create Location',
            'schema' => json_decode(Location::$schema),
            'uischema' => json_decode(Location::$uiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Location',
            'schema' => json_decode(Location::$schema),
            'uischema' => json_decode(Location::$uiSchema),
            'data' => json_decode($location),
            'resource' => $location,
            'routes' => $this->routes,
        ]);
    }
}
