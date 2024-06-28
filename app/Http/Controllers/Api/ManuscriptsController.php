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
            // TODO: force the transaction to fail if a duplicate ark is used
            // if (empty($request->json['ark']) || empty($request->json['shelfmark'])) {
            //     throw new \Exception('Ark or shelfmark is missing, transaction rolled back.');
            // }

            // extract metadata from the json field to populate their corresponding database columns
            $manuscript = Manuscript::create($this->_extractMetadataFromJsonField($request->json));

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
        return DB::transaction(function () use ($request, $manuscript) {
            // TODO: force the transaction to fail if a duplicate ark is used
            // if (empty($request->json['ark']) || empty($request->json['shelfmark'])) {
            //     throw new \Exception('Ark or shelfmark is missing, transaction rolled back.');
            // }

            // extract metadata from the json field to populate their corresponding database columns
            $manuscript->update($this->_extractMetadataFromJsonField($request->json));
 
            return new ManuscriptResource($manuscript);
        });
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

    private function _extractMetadataFromJsonField($jsonData)
    {
        // json data
        $metadata['json'] = json_encode($jsonData);
        $decodedJson = json_decode($metadata['json']);

        // ark
        $metadata['ark'] = $decodedJson && isset($decodedJson->ark) ? $decodedJson->ark : null;

        // shelfmark
        $metadata['shelfmark'] = null;
        if ($decodedJson && isset($decodedJson->idno) && is_array($decodedJson->idno)) {
            foreach ($decodedJson->idno as $item) {
                if (isset($item->type) && $item->type === 'shelfmark') {
                    $metadata['shelfmark'] = $item->value;
                    break;
                }
            }
        }

        return $metadata;
    }
}
