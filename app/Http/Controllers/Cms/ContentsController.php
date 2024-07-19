<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentRequest;
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
            'createEndpoint' => route('content-units.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Content Units > Add Content Unit',
            'schema' => json_decode(Content::$schema),
            'uischema' => json_decode(Content::$uiSchema),
            'saveEndpoint' => route('api.content-units.store'),
            'redirectUrl' => route('content-units.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $contentUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $contentUnit)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Content Units > Edit Content Unit',
            'schema' => json_decode(Content::$schema),
            'uischema' => json_decode(Content::$uiSchema),
            'data' => json_decode($contentUnit->json),
            'saveEndpoint' => route('api.content-units.update', $contentUnit->id),
            'redirectUrl' => route('content-units.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContentRequest $request, Content $contentUnit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $contentUnit)
    {
        //
    }
}
