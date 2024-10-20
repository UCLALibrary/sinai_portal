<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManuscriptJsonBatchUploadRequest;
use App\Http\Requests\ManuscriptJsonFileUploadRequest;
use App\Models\Manuscript;

class FilesController extends Controller
{
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
        
        $updatedManuscripts = array();
        $createdManuscripts = array();

        foreach ($files as $file) {

            $fileContent = $file->get();
            $metadata = json_decode($fileContent, true);
            $manuscriptId = basename($metadata['ark']);
            $manuscript = Manuscript::find($manuscriptId);

            if ($manuscript) {
                // update the resource
                $response = $manuscript->update([
                    'type' => $metadata['type']['label'],
                    'identifier' => $metadata['shelfmark'],
                    'json' => $fileContent,
                ]);

                if ($response) {
                    $updatedManuscripts[$manuscriptId] = $metadata['shelfmark'];
                }

            } else {
                // create the resource
                $response = Manuscript::create([
                    'id' => $manuscriptId,
                    'ark' => $metadata['ark'],
                    'type' => $metadata['type']['label'],
                    'identifier' => $metadata['shelfmark'],
                    'json' => $fileContent,
                ]);

                if ($response) {
                    $createdManuscripts[$manuscriptId] = $metadata['shelfmark'];
                }
            }
        }

        $status = count($updatedManuscripts) > 0 || $createdManuscripts > 0 ? 'success' : 'error';
        
        // create a list of all updated and created manuscripts for the response message
        $formattedMessage = '';
        if ($status === 'success') {
            $formattedMessage .= '</ul>';
            foreach ($createdManuscripts as $createdManuscriptId => $createdManuscriptShelfmark) {
                $formattedMessage .= '<li><b>' . $createdManuscriptId . '</b>: ' . $createdManuscriptShelfmark . ' has been created.</li>';
            }
            foreach ($updatedManuscripts as $updatedManuscriptId => $updatedManuscriptShelfmark) {
                $formattedMessage .= '<li><b>' . $updatedManuscriptId . '</b>: ' . $updatedManuscriptShelfmark . ' has been updated.</li>';
            }
            $formattedMessage .= '</ul>';
        }

        return response()->json([
            'status'   => $status,
            'message'  => $status === 'success' ? $formattedMessage : 'Error uploading JSON file(s).',
        ]);
    }
}
