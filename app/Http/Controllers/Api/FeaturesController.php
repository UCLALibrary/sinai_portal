<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeatureRequest;
use App\Http\Resources\FeatureResource;
use App\Models\Feature;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FeaturesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(FeatureRequest $request): FeatureResource
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Feature())->getFillableFields($request->json);

            // create the resource
            $feature = Feature::create($fields);

            $feature->formContexts()->sync($request->json['form_contexts'] ?? null);

            return new FeatureResource($feature);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeatureRequest $request, Feature $feature): FeatureResource
    {
        return DB::transaction(function () use ($request, $feature) {
            $fields = $feature->getFillableFields($request->json);

            // update the resource
            $feature->update($fields);

            $feature->formContexts()->sync($request->json['form_contexts'] ?? null);

            return new FeatureResource($feature);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $feature->delete();

        return $response
            ? response()->json(['message' => 'Feature deleted successfully'])
            : response()->json(['error' => 'Error deleting feature']);
    }
}
