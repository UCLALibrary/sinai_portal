<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptRequest;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;
use App\Models\Part;
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
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonField($request->json);

            // create the manuscript record
            $manuscript = Manuscript::create([
                'ark' => $metadata['ark'],
                'identifier' => $metadata['identifier'],
                'json' => $metadata['json'],
            ]);

            // insert the manuscript id into the json field
            $manuscript->json = json_encode(array_merge(json_decode($manuscript->json, true), ['id' => $manuscript->id]));
            $manuscript->save();

            // attach the manuscript to its corresponding parts
            $this->_attachManuscriptToParts($manuscript->id, $metadata['cod_units']);

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
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonField($request->json);

            // update the manuscript record
            $manuscript->update([
                'ark' => $metadata['ark'],
                'identifier' => $metadata['identifier'],
                'json' => $metadata['json'],
            ]);
 
            // attach the manuscript to its corresponding parts
            $this->_attachManuscriptToParts($manuscript->id, $metadata['cod_units']);

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

        if ($decodedJson) {
            // ark
            $metadata['ark'] = isset($decodedJson->ark) ? $decodedJson->ark : null;

            // identifier
            $metadata['identifier'] = null;
            if (isset($decodedJson->idno) && is_array($decodedJson->idno)) {
                foreach ($decodedJson->idno as $idno) {
                    $label = $idno->type === 'shelfmark'
                        ? 'Shelfmark'
                        : ($idno->type === 'part_no'
                            ? 'Part'
                            : 'UTO');
                    $metadata['identifier'] = $label . ': ' . $idno->value;
                    break;
                }
            }

            // parts
            $metadata['cod_units'] = isset($decodedJson->cod_units) ? $decodedJson->cod_units : null;
        }

        return $metadata;
    }

    private function _attachManuscriptToParts($manuscriptId, $partIds, $propertyName = 'ms_objs')
    {
        // attach the manuscript id to its corresponding parts
        $parts = Part::whereIn('id', $partIds)->get();
        foreach ($parts as $part) {
            JsonDataHelper::attachIdsToModelProperty($part, $propertyName, [$manuscriptId]);
        }
    }
}
