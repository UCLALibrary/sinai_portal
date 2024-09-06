<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LanguagesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageRequest $request): LanguageResource
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $language = Language::create([
                'id' => $metadata['id'],
                'label' => $metadata['label'],
                'iso' => $metadata['iso'],
                'glottolog' => $metadata['glottolog'],
                'writing_systems' => $metadata['writing_systems'],
                'other_names' => $metadata['other_names'],
                'when_in_use' => $metadata['when_in_use'],
                'regions' => $metadata['regions'],
            ]);

            $language->save();

            return new LanguageResource($language);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageRequest $request, Language $language): LanguageResource
    {
        return DB::transaction(function () use ($request, $language) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $language->update([
                'id' => $metadata['id'],
                'label' => $metadata['label'],
                'iso' => $metadata['iso'],
                'glottolog' => $metadata['glottolog'],
                'writing_systems' => $metadata['writing_systems'],
                'other_names' => $metadata['other_names'],
                'when_in_use' => $metadata['when_in_use'],
                'regions' => $metadata['regions'],
            ]);

            return new LanguageResource($language);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $language->delete();

        return $response
            ? response()->json(['message' => 'Language deleted successfully'])
            : response()->json(['error' => 'Error deleting language']);
    }

    private function _extractMetadataFromJsonData($jsonData): array
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['id'] = $jsonData['id'] ?? null;
            $metadata['label'] = $jsonData['label'] ?? null;
            $metadata['iso'] = $jsonData['iso'] ?? null;
            $metadata['glottolog'] = $jsonData['glottolog'] ?? null;
            $metadata['writing_systems'] = $jsonData['writing_systems'] ?? null;
            $metadata['other_names'] = $jsonData['other_names'] ?? null;
            $metadata['when_in_use'] = $jsonData['when_in_use'] ?? null;
            $metadata['regions'] = $jsonData['regions'] ?? null;
        }
        return $metadata;
    }
}
