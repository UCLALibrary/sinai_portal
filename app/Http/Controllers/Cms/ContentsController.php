<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Inertia\Inertia;

class ContentsController extends Controller
{
    protected $routes = [
        'index' => 'content-units.index',
        'create' => 'content-units.create',
        'store' => 'api.content-units.store',
        'edit' => 'content-units.edit',
        'update' => 'api.content-units.update',
        'upload' => [
            'store' => 'api.files.upload.store',
            'update' => 'api.files.upload.update',
            'batch' => 'api.files.upload.batch',
            'resourceType' => 'contentUnit',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Content Units',
            'resources' => Content::paginate(20),
            'columns' => [
                'ark' => 'Ark',
            ],
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Create Content Unit',
            'schema' => json_decode(Content::$schema),
            'uischema' => json_decode(Content::$uiSchema),
            'routes' => [
                'edit' => 'content-units.edit',
                'upload' => 'api.files.upload.store',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $contentUnit)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Content Unit',
            'schema' => json_decode(Content::$schema),
            'uischema' => json_decode(Content::$uiSchema),
            'data' => json_decode($contentUnit->json),
            'resource' => $contentUnit,
            'routes' => [
                'index' => 'content-units.index',
                'upload' => 'api.files.upload.update',
            ],
        ]);
    }
}
