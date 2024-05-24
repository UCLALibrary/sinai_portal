<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManuscriptsController extends Controller
{
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
        return Inertia::render('Manuscripts/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
        return Inertia::render('Manuscripts/CreateEdit', [
            'metadata' => json_decode($manuscript->json),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manuscript $manuscript)
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
