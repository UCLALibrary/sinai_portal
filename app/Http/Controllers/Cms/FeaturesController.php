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
                'id' => 'Id',
                'term' => 'Term',
                'corresp_note' => 'Note',
                'summary' => 'Summary',
                'scope' => 'Scope',
            ],
            'createEndpoint' => route('features.create'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feature $feature)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Features > Edit Feature',
            'schema' => json_decode(Feature::$schema),
            'uischema' => json_decode(Feature::$uiSchema),
            'data' => json_decode($feature),
            'saveEndpoint' => route('api.features.update', $feature->id),
            'redirectUrl' => route('features.index'),
        ]);
    }
}
