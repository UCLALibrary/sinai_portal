<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $resourceName)
    {
        // get the model class using the singular version of the resource name
        $modelClass = '\\App\\Models\\' . ucfirst(Str::singular($resourceName));

        return $modelClass::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $resourceName)
    {
        return DB::transaction(function () use ($request, $resourceName) {
            // get the model class using the singular version of the resource name
            $modelClass = '\\App\\Models\\' . ucfirst(Str::singular($resourceName));

            // populate the fillable fields for the resource
            $fields = (new $modelClass)->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $resource = $modelClass::create($fields);

            return $resource;
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $resourceName, string $resourceId)
    {
        return DB::transaction(function () use ($request, $resourceName, $resourceId) {
            // get the model class using the singular version of the resource name
            $modelClass = '\\App\\Models\\' . ucfirst(Str::singular($resourceName));

            // get the resource with the given id
            $resource = $modelClass::find($resourceId);

            // populate the fillable fields for the resource
            $fields = $resource->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $resource->update($fields);
 
            return $resource;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $resourceName, string $resourceId): JsonResponse
    {
        return DB::transaction(function () use ($resourceName, $resourceId) {
            // TODO: do we want to allow deletion or just soft delete?

            $resourceType = Str::singular($resourceName);

            // get the model class using the singular version of the resource name
            $modelClass = '\\App\\Models\\' . ucfirst($resourceType);

            // get the resource with the given id
            $resource = $modelClass::find($resourceId);

            $response = $resource->delete();
    
            return $response
                ? response()->json(['message' => ucfirst($resourceType) . ' deleted successfully'])
                : response()->json(['error' => 'Error deleting ' . $resourceType]);
        });
    }
}
