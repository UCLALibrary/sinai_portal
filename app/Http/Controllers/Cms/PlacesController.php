<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Inertia\Inertia;

class PlacesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Places',
            'resourceName' => 'places',
            'resources' => Place::paginate(20),
            'columns' => [
                'type' => 'Type',
                'pref_name' => 'Preferred Name'
            ],
            'routes' => [
                'create' => 'places.create',
            ],
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
            'routes' => [
                'index' => 'places.index',
                'store' => 'api.places.store',
            ],
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
            'routes' => [
                'index' => 'places.index',
                'update' => 'api.places.update',
            ],
        ]);
    }
}
