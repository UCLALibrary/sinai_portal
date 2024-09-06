<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Reference;
use Inertia\Inertia;

class ReferencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'References',
            'resourceName' => 'references',
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
            'createEndpoint' => route('references.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'References > Add Reference',
            'schema' => json_decode(Reference::$schema),
            'uischema' => json_decode(Reference::$uiSchema),
            'saveEndpoint' => route('api.references.store'),
            'redirectUrl' => route('references.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reference $reference)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'References > Edit Reference',
            'schema' => json_decode(Reference::$schema),
            'uischema' => json_decode(Reference::$uiSchema),
            'data' => json_decode($reference),
            'saveEndpoint' => route('api.references.update', $reference->id),
            'redirectUrl' => route('references.index'),
        ]);
    }

}
