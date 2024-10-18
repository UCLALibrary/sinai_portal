<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Inertia\Inertia;

class ContentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Content Units',
            'resourceName' => 'content-units',
            'resources' => Content::paginate(20),
            'columns' => [
                'ark' => 'Ark',
            ],
            'routes' => [
                'create' => 'content-units.create',
            ],
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
                'index' => 'content-units.index',
                'store' => 'api.content-units.store',
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
                'update' => 'api.content-units.update',
            ],
        ]);
    }
}
