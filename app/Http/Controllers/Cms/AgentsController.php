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
                'id' => 'Id',
                'type' => 'Type',
                'pref_name' => 'Preferred Name'
            ],
            'routes' => [
                'create' => 'agents.create',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Create Agent',
            'schema' => json_decode(Agent::$schema),
            'uischema' => json_decode(Agent::$uiSchema),
            'routes' => [
                'index' => 'agents.index',
                'store' => 'api.agents.store',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Agent',
            'schema' => json_decode(Agent::$schema),
            'uischema' => json_decode(Agent::$uiSchema),
            'data' => json_decode($agent->json),
            'resource' => $agent,
            'routes' => [
                'index' => 'agents.index',
                'update' => 'api.agents.update',
            ],
        ]);
    }
}
