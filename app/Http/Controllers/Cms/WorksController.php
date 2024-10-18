<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Inertia\Inertia;

class WorksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Works',
            'resourceName' => 'works',
            'resources' => Work::paginate(20),
            'columns' => [
                'pref_title' => 'Preferred Title',
            ],
            'createEndpoint' => route('works.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Works > Add Work',
            'schema' => json_decode(Work::$schema),
            'uischema' => json_decode(Work::$uiSchema),
            'routes' => [
                'index' => 'works.index',
                'store' => 'api.works.store',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Work $work)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Works > Edit Work',
            'schema' => json_decode(Work::$schema),
            'uischema' => json_decode(Work::$uiSchema),
            'data' => json_decode($work->json),
            'resource' => $work,
            'routes' => [
                'index' => 'works.index',
                'update' => 'api.works.update',
            ],
        ]);
    }
}
