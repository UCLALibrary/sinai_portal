<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptJsonBatchUploadRequest;
use App\Http\Requests\ManuscriptJsonFileUploadRequest;
use App\Http\Requests\ManuscriptRequest;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
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
    public function store(ManuscriptRequest $request): ManuscriptResource
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $manuscript = Manuscript::create([
                'ark' => $metadata['ark'],
                'identifier' => $metadata['identifier'],
                'json' => $metadata['json'],
            ]);

            // insert the id into the json field
            $manuscript->json = json_encode(array_merge(json_decode($manuscript->json, true), ['id' => $manuscript->id]));

            $manuscript->save();

            // attach the manuscript to its corresponding parts
            $this->_attachManuscriptToParts($manuscript->id, $metadata['cod_units']);

            return new ManuscriptResource($manuscript);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManuscriptRequest $request, Manuscript $manuscript): ManuscriptResource
    {
        return DB::transaction(function () use ($request, $manuscript) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
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
    public function destroy(Manuscript $manuscript): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $manuscript->delete();
 
        return $response
            ? response()->json(['message' => 'Manuscript deleted successfully'])
            : response()->json(['error' => 'Error deleting manuscript']);
    }

    /**
     * Store a newly created resource in storage on file upload.
     */
    public function storeOnUpload(ManuscriptJsonFileUploadRequest $request)
    {
        $file = $request->file('files');

        // decode the json file
        $json = $file->get();
        $metadata = json_decode($file->get(), true);

        // use the trailing ark segment as the manuscript id
        $manuscriptId = basename($metadata['ark']);

        // create the resource
        $manuscript = Manuscript::create([
            'id' => $manuscriptId,  
            'ark' => $metadata['ark'],
            'type' => $metadata['type']['label'],
            'identifier' => $metadata['shelfmark'],
            'json' => $json,
        ]);

        return response()->json([
            'status' => $manuscript ? 'success' : 'error',
            'message' => $manuscript ? 'The JSON file has been successfully uploaded.' : 'Error uploading JSON file for manuscript.',
            'resourceId' => $manuscriptId,
        ]);
    }

    /**
     * Update the specified resource in storage on file upload.
     */
    public function updateOnUpload(ManuscriptJsonFileUploadRequest $request, Manuscript $manuscript)
    {
        $file = $request->file('files');

        // decode the json file
        $json = $file->get();
        $metadata = json_decode($file->get(), true);

        // update the resource
        $response = $manuscript->update([
            'type' => $metadata['type']['label'],
            'identifier' => $metadata['shelfmark'],
            'json' => $json,
        ]);

        return response()->json([
            'status'   => $response ? 'success' : 'error',
            'message'  => $response ? 'The JSON file has been successfully uploaded.' : 'Error uploading JSON file for manuscript.',
            'resourceId' => basename($manuscript->ark),
        ]);
    }

    public function batchUpload(ManuscriptJsonBatchUploadRequest $request)
    {
        $files = $request->file('files');

        // TODO: create new records 
        foreach ($files as $file) {
            dd($file);


        //     $metadata = json_decode($file->get(), true);

        //     // update the resource
        //     $response = $manuscript->update([
        //         'type' => $metadata['type']['label'],
        //         'identifier' => $metadata['shelfmark'],
        //         'json' => $json,
        //     ]);



        //     $filename = $file->getClientOriginalName();
        //     $file->storeAs('uploads', $filename);
        }
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            // json
            $metadata['json'] = json_encode($jsonData);

            // ark
            $metadata['ark'] = isset($jsonData['ark']) ? $jsonData['ark'] : null;

            // identifier
            if (isset($jsonData['idno']) && is_array($jsonData['idno'])) {
                foreach ($jsonData['idno'] as $idno) {
                    $label = $idno['type'] === 'shelfmark'
                        ? 'Shelfmark'
                        : ($idno['type'] === 'part_no'
                            ? 'Part'
                            : ($idno['type'] === 'uto'
                                ? 'UTO'
                                : ''));
                    $metadata['identifier'] = $label . ': ' . $idno['value'];
                    break;
                }
            }

            // parts
            $metadata['cod_units'] = isset($jsonData['cod_units']) ? $jsonData['cod_units'] : null;
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
