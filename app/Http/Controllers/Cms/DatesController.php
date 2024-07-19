<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Date;
use Inertia\Inertia;

class DatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Dates',
            'resourceName' => 'dates',
            'resources' => Date::orderBy('created_at', 'desc')->orderBy('not_before', 'asc')->paginate(20),
            'columns' => [
                'type' => 'Type',
                'value' => 'Value',
                'as_written' => 'As written',
                'not_before' => 'Not before',
                'not_after' => 'Not after',
            ],
            'createEndpoint' => route('dates.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Dates > Add Date',
            'schema' => json_decode(Date::$schema),
            'uischema' => json_decode(Date::$uiSchema),
            'saveEndpoint' => route('api.dates.store'),
            'redirectUrl' => route('dates.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Date $date)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Dates > Edit Date',
            'schema' => json_decode(Date::$schema),
            'uischema' => json_decode(Date::$uiSchema),
            'data' => json_decode($date->json),
            'saveEndpoint' => route('api.dates.update', $date->id),
            'redirectUrl' => route('dates.index'),
        ]);
    }
}
