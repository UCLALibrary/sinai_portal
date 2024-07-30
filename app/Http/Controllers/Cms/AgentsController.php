<?php

namespace App\Http\Controllers\Cms;

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
        return Inertia::render('Resources/Index', [
            'title' => 'Agents',
            'resourceName' => 'agents',
            'resources' => Agent::paginate(20),
            'columns' => [
                'type' => 'Type',
                'pref_name' => 'Preferred Name'
            ],
            'createEndpoint' => route('agents.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Agents > Add Agent',
            'schema' => json_decode(Agent::$schema),
            'uischema' => json_decode(Agent::$uiSchema),
            'saveEndpoint' => route('api.agents.store'),
            'redirectUrl' => route('agents.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Agents > Edit Agent',
            'schema' => json_decode(Agent::$schema),
            'uischema' => json_decode(Agent::$uiSchema),
            'data' => json_decode($agent->json),
            'saveEndpoint' => route('api.agents.update', $agent->id),
            'redirectUrl' => route('agents.index'),
        ]);
    }
}
