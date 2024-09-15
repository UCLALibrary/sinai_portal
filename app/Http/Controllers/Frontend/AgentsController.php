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
