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
            $fields = (new Content())->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $contentUnit = Content::create($fields);

            return new ContentResource($contentUnit);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContentRequest $request, Content $contentUnit): ContentResource
    {
        return DB::transaction(function () use ($request, $contentUnit) {
            $fields = $contentUnit->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $contentUnit->update($fields);

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
}
