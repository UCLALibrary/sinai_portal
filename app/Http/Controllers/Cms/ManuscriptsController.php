<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptRequest;
use App\Models\Manuscript;
use Inertia\Inertia;

class ManuscriptsController extends Controller
{
    public function __construct()
    {
        // execute the static initialize to load the schema and ui schema
        Manuscript::initialize();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Manuscripts',
            'resourceName' => 'manuscripts',
            'resources' => Manuscript::paginate(20),
            'columns' => ['ark', 'shelfmark'],
            'createEndpoint' => route('manuscripts.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'schema' => json_decode(Manuscript::$schema),
            'uischema' => json_decode(Manuscript::$uischema),
            'saveEndpoint' => route('api.manuscripts.store'),
            'redirectUrl' => route('manuscripts.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ManuscriptRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Manuscript $manuscript)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manuscript $manuscript)
    {
        return Inertia::render('Resources/Edit', [
            'schema' => json_decode(Manuscript::$schema),
            'uischema' => json_decode(Manuscript::$uischema),
            'data' => json_decode($manuscript->json),
            'saveEndpoint' => route('api.manuscripts.update', $manuscript->id),
            'redirectUrl' => route('manuscripts.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManuscriptRequest $request, Manuscript $manuscript)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manuscript $manuscript)
    {
        //
    }
}
