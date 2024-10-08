<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationsController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return Inertia::render('Resources/Index', [
			'title' => 'Locations',
			'resourceName' => 'locations',
			'resources' => Location::paginate(20),
			'columns' => [
				'id' => 'ID',
				'collection' => 'Collection',
				'repository' => 'Repository',
				'note' => 'Note',
				'country' => 'Country'
			],
			'createEndpoint' => route('locations.create'),
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		return Inertia::render('Resources/Create', [
			'title' => 'Locations > Add Location',
			'schema' => json_decode(Location::$schema),
			'uischema' => json_decode(Location::$uiSchema),
			'saveEndpoint' => route('api.locations.store'),
			'redirectUrl' => route('locations.index'),
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Location $location)
	{
		return Inertia::render('Resources/Edit', [
			'title' => 'Locations > Edit Location',
			'schema' => json_decode(Location::$schema),
			'uischema' => json_decode(Location::$uiSchema),
			'data' => json_decode($location),
			'saveEndpoint' => route('api.locations.update', $location->id),
			'redirectUrl' => route('locations.index'),
		]);
	}
}
