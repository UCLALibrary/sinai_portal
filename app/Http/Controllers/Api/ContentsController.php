<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentRequest;
use App\Http\Resources\ContentResource;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ContentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContentResource::collection(Content::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContentRequest $request): ContentResource
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $contentUnit = Content::create($metadata);

            // insert the id into the json field
            $contentUnit->json = json_encode(array_merge(json_decode($contentUnit->json, true), ['id' => $contentUnit->id]));

            $contentUnit->save();

            return new ContentResource($contentUnit);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContentRequest $request, Content $contentUnit): ContentResource
    {
        return DB::transaction(function () use ($request, $contentUnit) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $contentUnit->update($metadata);

            return new ContentResource($contentUnit);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $contentUnit): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $contentUnit->delete();
 
        return $response
            ? response()->json(['message' => 'Content deleted successfully'])
            : response()->json(['error' => 'Error deleting content']);
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['ark'] = isset($jsonData['ark']) ? $jsonData['ark'] : null;
        }
        return $metadata;
    }
}
