<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationsController extends Controller
{

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return LocationResource::collection(Location::all());
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(LocationRequest $request): LocationResource
	{
		return DB::transaction(function () use ($request) {
			// extract metadata from the json field to populate database columns for list view
			$metadata = $this->_extractMetadataFromJsonData($request->json);

			// create the resource
			$location = Location::create([
				'id' => $metadata['id'],
				'collection' => $metadata['collection'],
				'repository' => $metadata['repository'],
				'note' => $metadata['note'],
				'country' => $metadata['country'],
				'address' => $metadata['address'],
				'contact_info' => $metadata['contact_info'],
				'coordinates' => $metadata['coordinates'],
				'url' => $metadata['url'],
			]);

			$location->save();

			return new LocationResource($location);
		});
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(LocationRequest $request, Location $location): LocationResource
	{
		return DB::transaction(function () use ($request, $location) {
			// extract metadata from the json field to populate database columns for list view
			$metadata = $this->_extractMetadataFromJsonData($request->json);

			// update the resource
			$location->update([
				'collection' => $metadata['collection'],
				'repository' => $metadata['repository'],
				'note' => $metadata['note'],
				'country' => $metadata['country'],
				'address' => $metadata['address'],
				'contact_info' => $metadata['contact_info'],
				'coordinates' => $metadata['coordinates'],
				'url' => $metadata['url'],
			]);

			return new LocationResource($location);
		});
	}

	private function _extractMetadataFromJsonData($jsonData): array
	{
		$metadata = [];
		if ($jsonData) {
			$metadata['id'] = $jsonData['id'] ?? null;
			$metadata['collection'] = $jsonData['collection'] ?? null;
			$metadata['repository'] = $jsonData['repository'] ?? null;
			$metadata['note'] = $jsonData['note'] ?? null;
			$metadata['country'] = $jsonData['country'] ?? null;
			$metadata['address'] = $jsonData['address'] ?? null;
			$metadata['contact_info'] = $jsonData['contact_info'] ?? null;
			$metadata['coordinates'] = $jsonData['coordinates'] ?? null;
			$metadata['url'] = $jsonData['url'] ?? null;
		}
		return $metadata;
	}
}
