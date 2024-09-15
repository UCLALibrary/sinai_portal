<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Inertia\Inertia;

class AgentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Frontend/Browse/Agents/Index', [
            'title' => 'Agents',
            'appId' => env('ALGOLIA_APP_ID'),
            'apiKey' => env('ALGOLIA_SECRET'),
            'indexName' => env('SCOUT_PREFIX') . 'agents',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent)
    {
        return Inertia::render('Frontend/Browse/Agents/Show', [
            'title' => 'Agent: ' . $agent->pref_name,
            'agent' => $agent,
        ]);
    }
}
