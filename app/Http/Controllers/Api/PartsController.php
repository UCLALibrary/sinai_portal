<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartRequest;
use App\Http\Resources\PartResource;
use App\Models\Part;
use Illuminate\Support\Facades\DB;

class PartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PartResource::collection(Part::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // TODO: on validation errors throw an exception to force the transaction to fail

            // save the json metadata
            $part = Part::create([
                // TODO: validate the json metadata
                // $request->validated()
                'json' => json_encode($request->json)
            ]);

            // extract fields from the json metadata into their corresponding database columns to display on list view 
            $this->_extractJsonFields();
    
            return new PartResource($part);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Part $codicologicalUnit)
    {
        return new PartResource($codicologicalUnit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartRequest $request, Part $codicologicalUnit)
    {
        $data = $request->validated();
        $data['json'] = json_encode($request->json);

        $codicologicalUnit->update($data);
 
        // extract fields from the json metadata into their corresponding database columns to display on list view
        $this->_extractJsonFields();

        return new PartResource($codicologicalUnit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $codicologicalUnit)
    {
        // TODO: do we want to allow deletion or just soft delete?

        $codicologicalUnit->delete();
 
        return response()->noContent();
    }

    private function _extractJsonFields()
    {
        Part::query()
            ->update([
                'ark' => Part::raw("json->>'ark'"),
                'identifier' => Part::raw("json->>'identifier'")
            ]);
    }
}
