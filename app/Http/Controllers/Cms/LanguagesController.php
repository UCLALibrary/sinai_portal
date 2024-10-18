<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Inertia\Inertia;

class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Languages',
            'resourceName' => 'languages',
            'resources' => Language::paginate(20),
            'columns' => [
                'id' => 'Id',
                'label' => 'Label',
                'iso' => 'ISO',
                'glottolog' => 'Glottolog',
                'writing_systems' => 'Writing Systems',
                'other_names' => 'Other names',
                'when_in_use' => 'When in use',
                'regions' => 'Regions',
            ],
            'routes' => [
                'create' => 'languages.create',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Create Language',
            'schema' => json_decode(Language::$schema),
            'uischema' => json_decode(Language::$uiSchema),
            'routes' => [
                'index' => 'languages.index',
                'store' => 'api.languages.store',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Edit Language',
            'schema' => json_decode(Language::$schema),
            'uischema' => json_decode(Language::$uiSchema),
            'data' => json_decode($language),
            'resource' => $language,
            'routes' => [
                'index' => 'languages.index',
                'update' => 'api.languages.update',
            ],
        ]);
    }
}
