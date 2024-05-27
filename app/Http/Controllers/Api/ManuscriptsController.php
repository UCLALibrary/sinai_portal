<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptRequest;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;
use Illuminate\Support\Facades\DB;

class ManuscriptsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ManuscriptResource::collection(Manuscript::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ManuscriptRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // save the json metadata
            $manuscript = Manuscript::create([
                // TODO: validate the json metadata
                // $request->validated()
                'json' => json_encode($request->json)
            ]);

            // TODO: force the transaction to fail if a duplicate ark is used
            // if (empty($request->json['ark']) || empty($request->json['shelfmark'])) {
            //     throw new \Exception('Ark or shelfmark is missing, transaction rolled back.');
            // }

            // extract fields from the json metadata into their corresponding database columns to display on list view 
            $this->_extractJsonFields();
    
            return new ManuscriptResource($manuscript);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Manuscript $manuscript)
    {
        return new ManuscriptResource($manuscript);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManuscriptRequest $request, Manuscript $manuscript)
    {
        $data = $request->validated();
        $data['json'] = json_encode($request->json);

        $manuscript->update($data);
 
        // extract fields from the json metadata into their corresponding database columns to display on list view
        $this->_extractJsonFields();

        return new ManuscriptResource($manuscript);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manuscript $manuscript)
    {
        // TODO: do we want to allow deletion or just soft delete?

        $manuscript->delete();
 
        return response()->noContent();
    }

    private function _extractJsonFields()
    {
        Manuscript::query()
            ->update([
                'ark' => Manuscript::raw("json->>'ark'"),
                'shelfmark' => Manuscript::raw("json->>'shelfmark'")
            ]);
    }
}
