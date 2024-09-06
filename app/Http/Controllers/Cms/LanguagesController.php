<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
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
            'createEndpoint' => route('languages.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Languages > Add Language',
            'schema' => json_decode(Language::$schema),
            'uischema' => json_decode(Language::$uiSchema),
            'saveEndpoint' => route('api.languages.store'),
            'redirectUrl' => route('languages.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Languages > Edit Language',
            'schema' => json_decode(Language::$schema),
            'uischema' => json_decode(Language::$uiSchema),
            'data' => json_decode($language),
            'saveEndpoint' => route('api.languages.update', $language->id),
            'redirectUrl' => route('languages.index'),
        ]);
    }
}
