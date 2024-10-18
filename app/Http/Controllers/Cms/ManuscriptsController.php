<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class ManuscriptsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Manuscripts',
            'resourceName' => 'manuscripts',
            'resources' => Manuscript::paginate(20),
            'columns' => [
                'ark' => 'ARK',
                'identifier' => 'Identifier'
            ],
            'routes' => [
                'create' => 'manuscripts.create',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Create Manuscript',
            'schema' => json_decode(Manuscript::$schema),
            'uischema' => json_decode(Manuscript::$uiSchema),
            'routes' => [
                'index' => 'manuscripts.index',
                'edit' => 'manuscripts.edit',
                'store' => 'api.manuscripts.store',
                'upload' => 'api.manuscripts.store.upload',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manuscript $manuscript)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Manuscript',
            'schema' => json_decode(Manuscript::$schema),
            'uischema' => json_decode(Manuscript::$uiSchema),
            'data' => json_decode($manuscript->json),
            'resource' => $manuscript,
            'routes' => [
                'index' => 'manuscripts.index',
                'update' => 'api.manuscripts.update',
                'upload' => 'api.manuscripts.update.upload',
            ],
        ]);
    }
}
