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
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $bibliography = Bibliography::create([
                'type' => $metadata['type'],
                'range' => $metadata['range'],
                'alt_shelf' => $metadata['alt_shelf'],
                'json' => $metadata['json'],
            ]);

            // insert the id into the json field
            $bibliography->json = json_encode(array_merge(json_decode($bibliography->json, true), ['id' => $bibliography->id]));

            $bibliography->save();

            return new BibliographyResource($bibliography);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BibliographyRequest $request, Bibliography $bibliography): BibliographyResource
    {
        return DB::transaction(function () use ($request, $bibliography) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $bibliography->update([
                'type' => $metadata['type'],
                'range' => $metadata['range'],
                'alt_shelf' => $metadata['alt_shelf'],
                'json' => $metadata['json'],
            ]);
 
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

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['type'] = isset($jsonData['type']) ? $jsonData['type'] : null;
            $metadata['range'] = isset($jsonData['range']) ? $jsonData['range'] : null;
            $metadata['alt_shelf'] = isset($jsonData['alt_shelf']) ? $jsonData['alt_shelf'] : null;
        }
        return $metadata;
    }
}
