<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Part;
use Inertia\Inertia;

class PartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Codicological Units',
            'resourceName' => 'codicological-units',
            'resources' => Part::paginate(20),
            'columns' => [
                'identifier' => 'Identifier',
                'ark' => 'ARK'
            ],
            'createEndpoint' => route('codicological-units.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Codicological Units > Add Codicological Unit',
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uiSchema),
            'saveEndpoint' => route('api.codicological-units.store'),
            'redirectUrl' => route('codicological-units.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Part $codicologicalUnit)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Codicological Units > Edit Codicological Unit',
            'schema' => json_decode(Part::$schema),
            'uischema' => json_decode(Part::$uiSchema),
            'data' => json_decode($codicologicalUnit->json),
            'saveEndpoint' => route('api.codicological-units.update', $codicologicalUnit->id),
            'redirectUrl' => route('codicological-units.index'),
        ]);
    }
}
