<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptRequest;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ManuscriptsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ManuscriptResource::collection(Manuscript::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ManuscriptRequest $request): ManuscriptResource
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Manuscript())->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $manuscript = Manuscript::create($fields);

            return new ManuscriptResource($manuscript);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManuscriptRequest $request, Manuscript $manuscript): ManuscriptResource
    {
        return DB::transaction(function () use ($request, $manuscript) {
            $fields = $manuscript->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $manuscript->update($fields);
 
            return new ManuscriptResource($manuscript);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manuscript $manuscript): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $manuscript->delete();
 
        return $response
            ? response()->json(['message' => 'Manuscript deleted successfully'])
            : response()->json(['error' => 'Error deleting manuscript']);
    }
}
