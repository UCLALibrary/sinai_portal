<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReferenceRequest;
use App\Http\Resources\ReferenceResource;
use App\Models\Reference;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReferencesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReferenceRequest $request): ReferenceResource
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Reference())->getFillableFields($request->json);

            // create the resource
            $reference = Reference::create($fields);

            return new ReferenceResource($reference);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReferenceRequest $request, Reference $reference): ReferenceResource
    {
        return DB::transaction(function () use ($request, $reference) {
            $fields = $reference->getFillableFields($request->json);

            // update the resource
            $reference->update($fields);

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
}
