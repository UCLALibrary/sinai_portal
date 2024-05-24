<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartRequest;
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
            'columns' => ['identifier', 'ark'],
            'createEndpoint' => route('codicological-units.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Parts/CreateEdit');
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
    public function show(Part $part)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Part $part)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartRequest $request, Part $part)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $part)
    {
        //
    }
}
