<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BibliographyRequest;
use App\Http\Resources\BibliographyResource;
use App\Models\Bibliography;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BibliographyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BibliographyResource::collection(Bibliography::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BibliographyRequest $request): BibliographyResource
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Bibliography())->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $bibliography = Bibliography::create($fields);

            return new BibliographyResource($bibliography);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BibliographyRequest $request, Bibliography $bibliography): BibliographyResource
    {
        return DB::transaction(function () use ($request, $bibliography) {
            $fields = $bibliography->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $bibliography->update($fields);
 
            return new BibliographyResource($bibliography);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bibliography $bibliography): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $bibliography->delete();
 
        return $response
            ? response()->json(['message' => 'Bibliography deleted successfully'])
            : response()->json(['error' => 'Error deleting bibliography']);
    }
}
