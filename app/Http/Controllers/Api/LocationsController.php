<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return LocationResource::collection(Location::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request): LocationResource
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Location())->getFillableFields($request->json);

            // create the resource
            $location = Location::create($fields);

            return new LocationResource($location);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, Location $location): LocationResource
    {
        return DB::transaction(function () use ($request, $location) {
            $fields = $location->getFillableFields($request->json);

            // update the resource
            $location->update($fields);

            return new LocationResource($location);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $location->delete();

        return $response
            ? response()->json(['message' => 'Location deleted successfully'])
            : response()->json(['error' => 'Error deleting location']);
    }
}
