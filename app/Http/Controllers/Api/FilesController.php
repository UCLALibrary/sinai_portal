<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JsonFileUploadRequest;
use App\Http\Requests\JsonBatchUploadRequest;

class FilesController extends Controller
{
    /**
     * Store a newly created resource in storage on file upload.
     */
    public function storeOnUpload(JsonFileUploadRequest $request, $resourceType)
    {
        $file = $request->file('files');

        // decode the json file
        $json = $file->get();
        $data = json_decode($json, true);

        // populate the fillable fields for the resource
        $model = '\\App\\Models\\' . ucfirst($resourceType);
        $instance = new $model();
        $fields = $instance->getFillableFields($data, $json);

        // create the resource
        $resource = $model::create($fields);

        return response()->json([
            'status' => $resource ? 'success' : 'error',
            'message' => $resource ? 'The JSON file has been successfully uploaded.' : 'Error uploading the JSON file.',
            'resourceId' => basename($data['ark']),
        ]);
    }

    /**
     * Update the specified resource in storage on file upload.
     */
    public function updateOnUpload(JsonFileUploadRequest $request, $resourceType, $resourceId)
    {
        $file = $request->file('files');

        // decode the json file
        $json = $file->get();
        $data = json_decode($json, true);

        // get the resource with the given id
        $model = '\\App\\Models\\' . ucfirst($resourceType);
        $resource = $model::find($resourceId);

        // populate the fillable fields for the resource
        $fields = $resource->getFillableFields($data, $json);

        // update the resource
        $status = $resource->update($fields);

        return response()->json([
            'status' => $status ? 'success' : 'error',
            'message' => $status ? 'The JSON file has been successfully uploaded.' : 'Error uploading the JSON file.',
        ]);
    }

    public function batchUpload(JsonBatchUploadRequest $request, $resourceType)
    {
        $messages = [];

        $files = $request->file('files');
        foreach ($files as $file) {
            // decode the json file
            $json = $file->get();
            $data = json_decode($json, true);

            // populate the fillable fields for the resource
            $model = '\\App\\Models\\' . ucfirst($resourceType);
            $instance = new $model();
            $fields = $instance->getFillableFields($data, $json);

            // get the resource with the given id
            $resourceId = basename($fields['id']);
            $resource = $model::find($resourceId);

            if ($resource) {
                // update the resource
                if ($resource->update($fields)) {
                    $messages[] = ucfirst($resourceType) . " with '" . $data['ark'] . "' has been updated.";
                }
            }
            else {
                // create the resource
                if ($model::create($fields)) {
                    $messages[] = ucfirst($resourceType) . " with '" . $data['ark'] . "' has been created.";
                    $createdResources[$resourceId] = $data['ark'];
                }
            }
        }

        return response()->json([
            'status' => count($messages) > 0 ? 'success' : 'error',
            'message' => count($messages) > 0 ? implode('<br>', $messages) : 'Error uploading JSON file(s).',
        ]);
    }
}
