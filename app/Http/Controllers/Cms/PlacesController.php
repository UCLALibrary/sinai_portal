<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceRequest;
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
                'event' => 'Event',
                'as_written' => 'As Written'
            ],
            'createEndpoint' => route('places.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Places > Add Place',
            'schema' => json_decode(Place::$schema),
            'uischema' => json_decode(Place::$uiSchema),
            'saveEndpoint' => route('api.places.store'),
            'redirectUrl' => route('places.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlaceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Places > Edit Place',
            'schema' => json_decode(Place::$schema),
            'uischema' => json_decode(Place::$uiSchema),
            'data' => json_decode($place->json),
            'saveEndpoint' => route('api.places.update', $place->id),
            'redirectUrl' => route('places.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlaceRequest $request, Place $place)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place)
    {
        //
    }
}
