<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
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
            'schema' => json_decode(Person::$schema),
            'uischema' => json_decode(Person::$uiSchema),
            'saveEndpoint' => route('api.persons.store'),
            'redirectUrl' => route('persons.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        return Inertia::render('Resources/Edit', [
            'schema' => json_decode(Person::$schema),
            'uischema' => json_decode(Person::$uiSchema),
            'data' => json_decode($person->json),
            'saveEndpoint' => route('api.persons.update', $person->id),
            'redirectUrl' => route('persons.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        //
    }
}
