<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $resourceName)
    {
        $modelClass = '\\App\\Models\\' . ucfirst(Str::singular($resourceName));

        return Inertia::render('Resources/Index', [
            'title' => ucfirst($resourceName),
            'resources' => $modelClass::orderBy('updated_at', 'desc')->paginate(20),
            'config' => $modelClass::$config,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $resourceName)
    {
        $resourceType = ucfirst(Str::singular($resourceName));
        $modelClass = '\\App\\Models\\' . $resourceType;

        return Inertia::render('Resources/Create', [
            'title' => 'Create ' . $resourceType,
            'schema' => json_decode($modelClass::$schema),
            'uischema' => json_decode($modelClass::$uiSchema),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $resourceName, string $resourceId)
    {
        $resourceType = ucfirst(Str::singular($resourceName));
        $modelClass = '\\App\\Models\\' . $resourceType;
        $resource = $modelClass::findOrFail($resourceId);

        return Inertia::render('Resources/Edit', [
            'title' => 'Edit ' . $resourceType,
            'schema' => json_decode($modelClass::$schema),
            'uischema' => json_decode($modelClass::$uiSchema),
            'data' => json_decode($resource->json) ?? $resource,
            'resource' => $resource,
        ]);
    }
}
