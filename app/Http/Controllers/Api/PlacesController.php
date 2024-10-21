<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PlacesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PlaceResource::collection(Place::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlaceRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Place())->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $place = Place::create($fields);

            return new PlaceResource($place);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlaceRequest $request, Place $place): JsonResponse
    {
        return DB::transaction(function () use ($request, $place) {
            $fields = $place->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $place->update($fields);

            return new PlaceResource($place);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $place->delete();
 
        return $response
            ? response()->json(['message' => 'Place deleted successfully'])
            : response()->json(['error' => 'Error deleting place']);
    }
}
