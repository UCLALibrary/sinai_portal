<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Reference;
use Inertia\Inertia;

class ReferencesController extends Controller
{
    protected $routes = [
        'index' => 'references.index',
        'create' => 'references.create',
        'store' => 'api.references.store',
        'edit' => 'references.edit',
        'update' => 'api.references.update',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'References',
            'resources' => Reference::paginate(20),
            'columns' => [
                'id' => 'Id',
                'short_title' => 'Short Title',
                'formatted_citation' => 'Formatted Citation',
                'zotero_uri' => 'Zotero URI',
                'date' => 'Date',
                'creator' => 'Creator',
                'category' => 'Category'
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
            'title' => 'Create Reference',
            'schema' => json_decode(Reference::$schema),
            'uischema' => json_decode(Reference::$uiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reference $reference)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Reference',
            'schema' => json_decode(Reference::$schema),
            'uischema' => json_decode(Reference::$uiSchema),
            'data' => json_decode($reference),
            'resource' => $reference,
            'routes' => $this->routes,
        ]);
    }

}
