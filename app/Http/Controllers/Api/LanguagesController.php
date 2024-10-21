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
            $fields = (new Language())->getFillableFields($request->json);

            // create the resource
            $language = Language::create($fields);

            return new LanguageResource($language);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageRequest $request, Language $language): LanguageResource
    {
        return DB::transaction(function () use ($request, $language) {
            $fields = $language->getFillableFields($request->json);

            // update the resource
            $language->update($fields);

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
}
