<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Bibliography;
use Inertia\Inertia;

class BibliographyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Bibliography',
            'resourceName' => 'bibliography',
            'resources' => Bibliography::paginate(20),
            'columns' => [
                'type' => 'Type',
                'alt_shelf' => 'Alternative Shelfmark',
                'range' => 'Range',
            ],
            'createEndpoint' => route('bibliography.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Bibliography > Add Bibliography',
            'schema' => json_decode(Bibliography::$schema),
            'uischema' => json_decode(Bibliography::$uiSchema),
            'saveEndpoint' => route('api.bibliography.store'),
            'redirectUrl' => route('bibliography.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bibliography $bibliography)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Bibliography > Edit Bibliography',
            'schema' => json_decode(Bibliography::$schema),
            'uischema' => json_decode(Bibliography::$uiSchema),
            'data' => json_decode($bibliography->json),
            'saveEndpoint' => route('api.bibliography.update', $bibliography->id),
            'redirectUrl' => route('bibliography.index'),
        ]);
    }
}
