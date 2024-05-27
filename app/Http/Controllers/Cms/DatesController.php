<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Date;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DatesController extends Controller
{
    public function __construct()
    {
        // execute the static initialize to load the schema and ui schema
        Date::initialize();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Dates',
            'resourceName' => 'dates',
            'resources' => Date::paginate(20),
            'columns' => ['as_written', 'not_before', 'not_after'],
            'createEndpoint' => route('dates.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dates/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Date $date)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Date $date)
    {
        return Inertia::render('Dates/CreateEdit', [
            'metadata' => $date->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Date $date)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Date $date)
    {
        //
    }
}
