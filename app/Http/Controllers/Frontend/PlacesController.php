<?php

namespace App\Http\Controllers\Frontend;

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
        return Inertia::render('Frontend/Browse/Places/Index', [
            'title' => 'Places',
            'appId' => env('ALGOLIA_APP_ID'),
            'apiKey' => env('ALGOLIA_SECRET'),
            'indexName' => env('SCOUT_PREFIX') . 'places',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        return Inertia::render('Frontend/Browse/Places/Show', [
            'title' => 'Place: ' . $place->pref_name,
            'place' => $place,
            'last_modified' => \Carbon\Carbon::parse($place->updated_at)->format('F j, Y')
        ]);
    }
}
