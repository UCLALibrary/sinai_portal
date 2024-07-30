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
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $place = Place::create($metadata);

            // insert the id into the json field
            $place->json = json_encode(array_merge(json_decode($place->json, true), ['id' => $place->id]));

            $place->save();

            return new PlaceResource($place);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlaceRequest $request, Place $place): JsonResponse
    {
        return DB::transaction(function () use ($request, $place) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $place->update($metadata);

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

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['type'] = isset($jsonData['type']) ? $jsonData['type'] : null;
            $metadata['pref_name'] = isset($jsonData['pref_name']) ? $jsonData['pref_name'] : null;
        }
        return $metadata;
    }
}
