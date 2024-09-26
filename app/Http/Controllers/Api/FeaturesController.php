<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeatureRequest;
use App\Http\Resources\FeatureResource;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(FeatureRequest $request): FeatureResource
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $feature = Feature::create([
                'term' => $metadata['term'],
                'corresp_note' => $metadata['corresp_note'],
                'summary' => $metadata['summary'],
                'scope' => $metadata['scope']
            ]);

            $feature->save();

            $feature->formContexts()->sync($metadata['form_contexts']);

            return new FeatureResource($feature);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeatureRequest $request, Feature $feature): FeatureResource
    {
        return DB::transaction(function () use ($request, $feature) {

            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $feature->update([
                'term' => $metadata['term'],
                'corresp_note' => $metadata['corresp_note'],
                'summary' => $metadata['summary'],
                'scope' => $metadata['scope']
            ]);

            $feature->formContexts()->sync($metadata['form_contexts']);

            return new FeatureResource($feature);
        });
    }

    private function _extractMetadataFromJsonData($jsonData): array
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['term'] = $jsonData['term'] ?? null;
            $metadata['corresp_note'] = $jsonData['corresp_note'] ?? null;
            $metadata['summary'] = $jsonData['summary'] ?? null;
            $metadata['scope'] = $jsonData['scope'] ?? null;
            $metadata['form_contexts'] = $jsonData['form_contexts'] ?? null;
        }
        return $metadata;
    }
}
