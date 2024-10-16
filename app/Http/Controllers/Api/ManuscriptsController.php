<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptRequest;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
     * Replace the resource data.
     */
    public function upload(Request $request, Manuscript $manuscript)
    {
        // TODO: do not allow uploading of files with mismatching arks

        $file = $request->file('files');

        $jsonContent = File::get($file->getRealPath());
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'Error decoding JSON from file: ' . $file->getFilename();
            return request()->inertia()
                ? redirect()->back()->with('error', $error)
                : response()->json(['error' => $error]);
        }

        $ark = $data['ark'] ?? null;
        $type = $data['type']['label'] ?? null;
        $identifier = $data['shelfmark'] ?? null;

        if (!$ark || !$type || !$identifier) {
            $error = 'Missing "ark", "type", or "identifier" in file: ' . $file->getFilename();
            return request()->inertia()
                ? redirect()->back()->with('error', $error)
                : response()->json(['error' => $error]);
        }
        
        $arkSegments = explode('/', $ark);

        // update the resource
        $response = $manuscript->update([
            'id' => end($arkSegments),
            'ark' => $ark,
            'type' => $type,
            'identifier' => $identifier,
            'json' => $jsonContent,
        ]);

        if ($response) {
            $message = 'The JSON file has been successfully uploaded.';
            $status = 'success';
        } else {
            $message = 'Error uploading JSON file for manuscript.';
            $status = 'error';
        }

        return request()->inertia()
            ? redirect()->back()->with($status, $message)
            : response()->json([$status => $message]);
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
