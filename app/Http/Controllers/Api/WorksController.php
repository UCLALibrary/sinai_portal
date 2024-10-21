<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkRequest;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WorksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return WorkResource::collection(Work::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkRequest $request): WorkResource
    {
        return DB::transaction(function () use ($request) {
            $fields = (new Work())->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $work = Work::create($fields);

            return new WorkResource($work);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkRequest $request, Work $work): WorkResource
    {
        return DB::transaction(function () use ($request, $work) {
            $fields = $work->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $work->update($fields);
 
            return new WorkResource($work);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Work $work): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $work->delete();
 
        return $response
            ? response()->json(['message' => 'Work deleted successfully'])
            : response()->json(['error' => 'Error deleting work']);
    }
}
