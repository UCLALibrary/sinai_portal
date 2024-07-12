<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DateRequest;
use App\Http\Resources\DateResource;
use App\Models\Date;
use Illuminate\Support\Facades\DB;

class DatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dates = Date::orderBy('not_before')->get();
    
        return DateResource::collection($dates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DateRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // TODO: on validation errors throw an exception to force the transaction to fail

            // save the json metadata
            $date = Date::create([
                // TODO: validate the json metadata
                // $request->validated()
                'json' => json_encode($request->json)
            ]);

            // extract fields from the json metadata into their corresponding database columns to display on list view 
            $this->_extractJsonFields();
    
            return new DateResource($date);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Date $date)
    {
        return new DateResource($date);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DateRequest $request, Date $date)
    {
        $data = $request->validated();
        $data['json'] = json_encode($request->json);

        $date->update($data);
 
        // extract fields from the json metadata into their corresponding database columns to display on list view
        $this->_extractJsonFields();

        return new DateResource($date);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Date $date)
    {
        // TODO: do we want to allow deletion or just soft delete?

        $date->delete();
 
        return response()->noContent();
    }

    private function _extractJsonFields()
    {
        Date::query()
            ->update([
                'type' => Date::raw("json->>'type'"),
                'as_written' => Date::raw("json->>'as_written'"),
                'not_before' => Date::raw("json->>'not_before'"),
                'not_after' => Date::raw("json->>'not_after'"),
                'note' => Date::raw("json->>'note'"),
            ]);
    }
}
