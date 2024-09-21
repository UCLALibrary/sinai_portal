<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Inertia\Inertia;

class WorksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Frontend/Browse/Works/Index', [
            'title' => 'Works',
            'appId' => env('ALGOLIA_APP_ID'),
            'apiKey' => env('ALGOLIA_SECRET'),
            'indexName' => env('SCOUT_PREFIX') . 'works',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Work $work)
    {
        return Inertia::render('Frontend/Browse/Works/Show', [
            'title' => 'Work: ' . $work->pref_title,
            'work' => $work,
            'last_modified' => \Carbon\Carbon::parse($work->updated_at)->format('F j, Y')
        ]);
    }
}
