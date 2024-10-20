<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Inertia\Inertia;

class WorksController extends Controller
{
    protected $routes = [
        'index' => 'works.index',
        'create' => 'works.create',
        'store' => 'api.works.store',
        'edit' => 'works.edit',
        'update' => 'api.works.update',
        'upload' => [
            'store' => 'api.files.upload.store',
            'update' => 'api.files.upload.update',
            'batch' => 'api.files.upload.batch',
            'resourceType' => 'work',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Works',
            'resources' => Work::paginate(20),
            'columns' => [
                'pref_title' => 'Preferred Title',
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
            'title' => 'Create Work',
            'schema' => json_decode(Work::$schema),
            'uischema' => json_decode(Work::$uiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Work $work)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Work',
            'schema' => json_decode(Work::$schema),
            'uischema' => json_decode(Work::$uiSchema),
            'data' => json_decode($work->json),
            'resource' => $work,
            'routes' => $this->routes,
        ]);
    }
}
