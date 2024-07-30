<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartRequest;
use App\Http\Resources\PartResource;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PartResource::collection(Part::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $part = Part::create($metadata);

            // insert the id into the json field
            $part->json = json_encode(array_merge(json_decode($part->json, true), ['id' => $part->id]));

            $part->save();

            return new PartResource($part);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartRequest $request, Part $codicologicalUnit): PartResource
    {
        return DB::transaction(function () use ($request, $codicologicalUnit) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $codicologicalUnit->update($metadata);

            return new PartResource($codicologicalUnit);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $codicologicalUnit): PartResource
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $codicologicalUnit->delete();
 
        return $response
            ? response()->json(['message' => 'Part deleted successfully'])
            : response()->json(['error' => 'Error deleting part']);
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            // json
            $metadata['json'] = json_encode($jsonData);

            // ark
            $metadata['ark'] = isset($jsonData['ark']) ? $jsonData['ark'] : null;

            // identifier
            if (isset($jsonData['idno']) && is_array($jsonData['idno'])) {
                foreach ($jsonData['idno'] as $idno) {
                    $label = $idno['type'] === 'shelfmark'
                        ? 'Shelfmark'
                        : ($idno['type'] === 'part_no'
                            ? 'Part'
                            : ($idno['type'] === 'uto_mark'
                                ? 'UTO Mark'
                                : ''));
                    $metadata['identifier'] = $label . ': ' . $idno['value'];
                    break;
                }
            }
        }
        return $metadata;
    }
}
