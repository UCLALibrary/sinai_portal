<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceRequest;
use App\Http\Resources\ReferenceResource;
use App\Models\Reference;
use Illuminate\Support\Facades\DB;

class ReferencesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReferenceRequest $request): ReferenceResource
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $reference = Reference::create([
                'short_title' => $metadata['short_title'],
                'formatted_citation' => $metadata['formatted_citation'],
                'zotero_uri' => $metadata['zotero_uri'],
                'date' => $metadata['date'],
                'creator' => $metadata['creator'],
                'category' => $metadata['category']
            ]);

            $reference->save();

            return new ReferenceResource($reference);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReferenceRequest $request, Reference $reference): ReferenceResource
    {
        return DB::transaction(function () use ($request, $reference) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $reference->update([
                'short_title' => $metadata['short_title'],
                'formatted_citation' => $metadata['formatted_citation'],
                'zotero_uri' => $metadata['zotero_uri'],
                'date' => $metadata['date'],
                'creator' => $metadata['creator'],
                'category' => $metadata['category'],
            ]);

            return new ReferenceResource($reference);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reference $reference): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $reference->delete();

        return $response
            ? response()->json(['message' => 'Reference deleted successfully'])
            : response()->json(['error' => 'Error deleting reference']);
    }

    private function _extractMetadataFromJsonData($jsonData): array
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['short_title'] = $jsonData['short_title'] ?? null;
            $metadata['formatted_citation'] = $jsonData['formatted_citation'] ?? null;
            $metadata['zotero_uri'] = $jsonData['zotero_uri'] ?? null;
            $metadata['date'] = $jsonData['date'] ?? null;
            $metadata['creator'] = $jsonData['creator'] ?? null;
            $metadata['category'] = $jsonData['category'] ?? null;
        }
        return $metadata;
    }
}
