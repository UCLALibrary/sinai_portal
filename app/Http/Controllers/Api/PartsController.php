<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartRequest;
use App\Http\Resources\PartResource;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PartResource::collection(Part::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Part())->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $part = Part::create($fields);

            return new PartResource($part);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartRequest $request, Part $codicologicalUnit): PartResource
    {
        return DB::transaction(function () use ($request, $codicologicalUnit) {
            $fields = $codicologicalUnit->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $codicologicalUnit->update($fields);

            return new PartResource($codicologicalUnit);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $codicologicalUnit): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $codicologicalUnit->delete();
 
        return $response
            ? response()->json(['message' => 'Part deleted successfully'])
            : response()->json(['error' => 'Error deleting part']);
    }
}
