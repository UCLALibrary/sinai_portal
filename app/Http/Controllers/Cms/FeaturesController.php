<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Inertia\Inertia;

class FeaturesController extends Controller
{
    protected $routes = [
        'index' => 'features.index',
        'create' => 'features.create',
        'store' => 'api.features.store',
        'edit' => 'features.edit',
        'update' => 'api.features.update',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Features',
            'resources' => Feature::paginate(20),
            'columns' => [
                'label' => 'Label',
                'corresp_note' => 'Note',
                'summary' => 'Summary',
                'scope' => 'Scope'
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
            'title' => 'Create Feature',
            'schema' => json_decode(Feature::$schema),
            'uischema' => json_decode(Feature::$uiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feature $feature)
    {
        // Fetch the feature with related formContexts, but only retrieve IDs for formContexts
        $feature = Feature::with('formContexts:id')->find($feature->id)->toArray();
        $feature['form_contexts'] = array_column($feature['form_contexts'], 'id');

        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Feature',
            'schema' => json_decode(Feature::$schema),
            'uischema' => json_decode(Feature::$uiSchema),
            'data' => $feature,
            'resource' => $feature,
            'routes' => $this->routes,
        ]);
    }
}
