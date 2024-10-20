<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Inertia\Inertia;

class AgentsController extends Controller
{
    protected $routes = [
        'index' => 'agents.index',
        'create' => 'agents.create',
        'store' => 'api.agents.store',
        'edit' => 'agents.edit',
        'update' => 'api.agents.update',
        'upload' => [
            'store' => 'api.files.upload.store',
            'update' => 'api.files.upload.update',
            'batch' => 'api.files.upload.batch',
            'resourceType' => 'agent',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Agents',
            'resources' => Agent::paginate(20),
            'columns' => [
                'id' => 'Id',
                'type' => 'Type',
                'pref_name' => 'Preferred Name'
            ],
            'routes' => $this->routes,
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
            'routes' => $this->routes,
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
            'routes' => $this->routes,
        ]);
    }
}
