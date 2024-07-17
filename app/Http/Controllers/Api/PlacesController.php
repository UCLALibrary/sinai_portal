<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
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
    public function store(PlaceRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // save the json metadata
            $place = Place::create($metadata);

            // insert the manuscript id into the json field
            $place->json = json_encode(array_merge(json_decode($place->json, true), ['id' => $place->id]));
            $place->save();

            return new PlaceResource($place);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        return new PlaceResource($place);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlaceRequest $request, Place $place)
    {
        return DB::transaction(function () use ($request, $place) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // save the json metadata
            $place->update($metadata);

            return new PlaceResource($place);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place)
    {
        // TODO: do we want to allow deletion or just soft delete?

        $place->delete();
 
        return response()->noContent();
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['event'] = isset($jsonData['event']) ? $jsonData['event'] : null;
            $metadata['as_written'] = isset($jsonData['as_written']) ? $jsonData['as_written'] : null;
        }
        return $metadata;
    }
}
