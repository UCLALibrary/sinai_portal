<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $resourceName)
    {
        $modelClass = '\\App\\Models\\' . Str::studly(Str::singular($resourceName));

        if (!$request->inertia() && $request->expectsJson()) {
            // return json response if request is not an Inertia request
            return $modelClass::all();
        }

        return Inertia::render('Resources/Index', [
            'title' => ucwords(str_replace('-', ' ', $resourceName)),
            'resources' => $modelClass::orderBy('updated_at', 'desc')->paginate(20),
            'config' => $modelClass::$config,
        ]);   
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $resourceName)
    {
        $resourceType = Str::studly(Str::singular($resourceName));
        $modelClass = '\\App\\Models\\' . $resourceType;

        return Inertia::render('Resources/Create', [
            'title' => 'Create ' . ucwords(str_replace('-', ' ', Str::singular($resourceName))),
            'schema' => json_decode($modelClass::$createSchema ?? $modelClass::$schema),
            'uischema' => json_decode($modelClass::$createUiSchema ?? $modelClass::$uiSchema),
            'config' => $modelClass::$config,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $resourceName)
    {
        return DB::transaction(function () use ($request, $resourceName) {
            // get the model class using the singular version of the resource name
            $modelClass = '\\App\\Models\\' . Str::studly(Str::singular($resourceName));

            // populate the fillable fields for the resource
            $fields = (new $modelClass)->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $resource = $modelClass::create($fields);

            return $resource;
        });
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $resourceName, string $resourceId)
    {
        $resourceType = Str::studly(Str::singular($resourceName));
        $modelClass = '\\App\\Models\\' . $resourceType;
        $resource = $modelClass::findOrFail($resourceId);

        $label = $resource->identifier  // manuscripts | layers
            ?? $resource->label         // text units | languages | scripts
            ?? $resource->pref_title    // works
            ?? $resource->pref_name     // agents | places
            ?? $resource->alt_shelf     // bibliography
            ?? $resource->short_title;  // references
            
        if (!$label && $resource->collection && $resource->repository) {
            $label = $resource->collection . ', ' . $resource->repository;  // locations
        }

        return Inertia::render('Resources/Edit', [
            'title' => 'Edit ' . ucwords(str_replace('-', ' ', Str::singular($resourceName))) . ($label ? ': ' . $label : ''),
            'schema' => json_decode($modelClass::$editSchema ?? $modelClass::$schema),
            'uischema' => json_decode($modelClass::$editUiSchema ?? $modelClass::$uiSchema),
            'data' => json_decode($resource->json) ?? $resource,
            'resource' => $resource,
            'config' => $modelClass::$config,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $resourceName, string $resourceId)
    {
        return DB::transaction(function () use ($request, $resourceName, $resourceId) {
            // get the model class using the singular version of the resource name
            $modelClass = '\\App\\Models\\' . Str::studly(Str::singular($resourceName));

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
