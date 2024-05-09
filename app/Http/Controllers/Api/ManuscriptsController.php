<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptRequest;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;

class ManuscriptsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ManuscriptResource::collection(Manuscript::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ManuscriptRequest $request)
    {
        $manuscript = Manuscript::create($request->validated());
 
        return new ManuscriptResource($manuscript);
    }

    /**
     * Display the specified resource.
     */
    public function show(Manuscript $manuscript)
    {
        return new ManuscriptResource($manuscript);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManuscriptRequest $request, Manuscript $manuscript)
    {
        $manuscript->update($request->validated());
 
        return new ManuscriptResource($manuscript);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manuscript $manuscript)
    {
        $manuscript->delete();
 
        return response()->noContent();
    }
}
