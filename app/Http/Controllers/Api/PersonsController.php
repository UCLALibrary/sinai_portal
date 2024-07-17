<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PersonResource::collection(Person::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // save the json metadata
            $person = Person::create($metadata);

            // insert the manuscript id into the json field
            $person->json = json_encode(array_merge(json_decode($person->json, true), ['id' => $person->id]));
            $person->save();

            return new PersonResource($person);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        return new PersonResource($person);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $request, Person $person)
    {
        return DB::transaction(function () use ($request, $person) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // save the json metadata
            $person->update($metadata);

            return new PersonResource($person);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        // TODO: do we want to allow deletion or just soft delete?

        $person->delete();
 
        return response()->noContent();
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['role'] = isset($jsonData['role']) ? $jsonData['role'] : null;
            $metadata['as_written'] = isset($jsonData['as_written']) ? $jsonData['as_written'] : null;
        }
        return $metadata;
    }
}
