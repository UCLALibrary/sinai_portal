<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TextUnit;
use Inertia\Inertia;

class TextUnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Frontend/Browse/TextUnits/Index', [
            'title' => 'Text Units',
            'appId' => env('ALGOLIA_APP_ID'),
            'apiKey' => env('ALGOLIA_SECRET'),
            'indexName' => env('SCOUT_PREFIX') . 'text_units',
            'searchQuery' => request('q') ?? '',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TextUnit $textunit)
    {
        return Inertia::render('Frontend/Browse/TextUnits/Show', [
            'title' => $textunit->label,
            'textUnit' => $textunit,
            'last_modified' => \Carbon\Carbon::parse($textunit->updated_at)->format('F j, Y')
        ]);
    }
}
