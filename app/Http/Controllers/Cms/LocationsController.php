<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Inertia\Inertia;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Locations',
            'resourceName' => 'locations',
            'resources' => Location::paginate(20),
            'columns' => [
                'id' => 'ID',
                'collection' => 'Collection',
                'repository' => 'Repository',
                'note' => 'Note',
                'country' => 'Country'
            ],
            'routes' => [
                'create' => 'locations.create',
            ],
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
            'routes' => [
                'index' => 'locations.index',
                'store' => 'api.locations.store',
            ],
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
            'routes' => [
                'index' => 'locations.index',
                'update' => 'api.locations.update',
            ],
        ]);
    }
}
