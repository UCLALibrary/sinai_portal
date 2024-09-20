<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Inertia\Inertia;

class FeaturesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Features',
            'resourceName' => 'features',
            'resources' => Feature::paginate(20),
            'columns' => [
                'term' => 'Term',
                'corresp_note' => 'Note',
                'summary' => 'Summary',
                'scope' => 'Scope'
            ],
            'createEndpoint' => route('features.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Features > Add Feature',
            'schema' => json_decode(Feature::$schema),
            'uischema' => json_decode(Feature::$uiSchema),
            'saveEndpoint' => route('api.features.store'),
            'redirectUrl' => route('features.index'),
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
            'title' => 'Features > Edit Feature',
            'schema' => json_decode(Feature::$schema),
            'uischema' => json_decode(Feature::$uiSchema),
            'data' => $feature,
            'saveEndpoint' => route('api.features.update', $feature['id']),
            'redirectUrl' => route('features.index'),
        ]);
    }
}
