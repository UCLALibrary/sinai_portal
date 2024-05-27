<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartRequest;
use App\Models\Part;
use Inertia\Inertia;

class PartsController extends Controller
{
    public function __construct()
    {
        // execute the static initialize to load the schema and ui schema
        Part::initialize();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Codicological Units',
            'resourceName' => 'codicological-units',
            'resources' => Part::paginate(20),
            'columns' => ['identifier', 'ark'],
            'createEndpoint' => route('codicological-units.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uischema),
            'saveEndpoint' => route('api.codicological-units.store'),
            'redirectUrl' => route('codicological-units.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Part $codicologicalUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Part $codicologicalUnit)
    {
        return Inertia::render('Resources/Edit', [
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uischema),
            'data' => json_decode($codicologicalUnit->json),
            'saveEndpoint' => route('api.codicological-units.update', $codicologicalUnit->id),
            'redirectUrl' => route('codicological-units.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartRequest $request, Part $codicologicalUnit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $codicologicalUnit)
    {
        //
    }
}
