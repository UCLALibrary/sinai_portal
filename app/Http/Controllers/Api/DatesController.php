<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DateRequest;
use App\Http\Resources\DateResource;
use App\Models\Date;
use Illuminate\Support\Facades\DB;

class DatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dates = Date::orderBy('not_before')->get();
    
        return DateResource::collection($dates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DateRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // save the json metadata
            $date = Date::create($metadata);

            // insert the id into the json field
            $date->json = json_encode(array_merge(json_decode($date->json, true), ['id' => $date->id]));
            $date->save();

            return new DateResource($date);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DateRequest $request, Date $date)
    {
        return DB::transaction(function () use ($request, $date) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // save the json metadata
            $date->update($metadata);

            return new DateResource($date);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Date $date)
    {
        // TODO: do we want to allow deletion or just soft delete?

        $date->delete();
 
        return response()->noContent();
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['type'] = isset($jsonData['type']) ? $jsonData['type'] : null;
            $metadata['not_before'] = isset($jsonData['iso']) && isset($jsonData['iso']['not_before']) ? $jsonData['iso']['not_before'] : null;
            $metadata['not_after'] = isset($jsonData['iso']) && isset($jsonData['iso']['not_after']) ? $jsonData['iso']['not_after'] : null;
            $metadata['value'] = isset($jsonData['value']) ? $jsonData['value'] : null;
            $metadata['as_written'] = isset($jsonData['as_written']) ? $jsonData['as_written'] : null;
        }
        return $metadata;
    }
}
