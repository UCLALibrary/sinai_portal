<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Inertia\Inertia;

class PersonsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Persons',
            'resourceName' => 'persons',
            'resources' => Person::paginate(20),
            'columns' => [
                'role' => 'Role',
                'as_written' => 'As Written'
            ],
            'createEndpoint' => route('persons.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Persons > Add Person',
            'schema' => json_decode(Person::$schema),
            'uischema' => json_decode(Person::$uiSchema),
            'saveEndpoint' => route('api.persons.store'),
            'redirectUrl' => route('persons.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Persons > Edit Person',
            'schema' => json_decode(Person::$schema),
            'uischema' => json_decode(Person::$uiSchema),
            'data' => json_decode($person->json),
            'saveEndpoint' => route('api.persons.update', $person->id),
            'redirectUrl' => route('persons.index'),
        ]);
    }
}
