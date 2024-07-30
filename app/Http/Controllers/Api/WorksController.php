<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkRequest;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WorksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return WorkResource::collection(Work::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkRequest $request): WorkResource
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $work = Work::create([
                'pref_title' => $metadata['pref_title'],
                'json' => $metadata['json'],
            ]);

            // insert the id into the json field
            $work->json = json_encode(array_merge(json_decode($work->json, true), ['id' => $work->id]));

            $work->save();

            return new WorkResource($work);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkRequest $request, Work $work): WorkResource
    {
        return DB::transaction(function () use ($request, $work) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $work->update([
                'pref_title' => $metadata['pref_title'],
                'json' => $metadata['json'],
            ]);
 
            return new WorkResource($work);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Work $work): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $work->delete();
 
        return $response
            ? response()->json(['message' => 'Work deleted successfully'])
            : response()->json(['error' => 'Error deleting work']);
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['pref_title'] = isset($jsonData['pref_title']) ? $jsonData['pref_title'] : null;
        }
        return $metadata;
    }
}
