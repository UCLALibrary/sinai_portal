<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use Inertia\Inertia;

class ManuscriptsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Frontend/Browse/Manuscripts/Index', [
            'title' => 'Manuscripts',
            'appId' => env('ALGOLIA_APP_ID'),
            'apiKey' => env('ALGOLIA_SECRET'),
            'indexName' => env('SCOUT_PREFIX') . 'manuscripts',
            'searchQuery' => request('q') ?? '',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Manuscript $manuscript)
    {
        return Inertia::render('Frontend/Browse/Manuscripts/Show', [
            'title' => $manuscript->type . ': ' . $manuscript->identifier,
            'manuscript' => $manuscript,
            'last_modified' => \Carbon\Carbon::parse($manuscript->updated_at)->format('F j, Y')
        ]);
    }
}
