<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Layer;
use Inertia\Inertia;

class LayersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Frontend/Browse/Layers/Index', [
            'title' => 'Layers',
            'appId' => env('ALGOLIA_APP_ID'),
            'apiKey' => env('ALGOLIA_SECRET'),
            'indexName' => env('SCOUT_PREFIX') . 'layers',
            'searchQuery' => request('q') ?? '',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Layer $layer)
    {
        return Inertia::render('Frontend/Browse/Layers/Show', [
            'title' => $layer->identifier,
            'layer' => $layer,
            'source' => $layer->getSourceIdentifiers(),
            'last_modified' => \Carbon\Carbon::parse($layer->updated_at)->format('F j, Y')
        ]);
    }
}
