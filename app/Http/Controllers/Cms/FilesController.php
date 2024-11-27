<?php

namespace App\Http\Controllers\Cms;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\JsonBatchUploadRequest;
use App\Http\Requests\JsonFileUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Opis\JsonSchema\Validator;
use Opis\JsonSchema\Errors\ErrorFormatter;

class FilesController extends Controller
{
    /**
     * Store a newly created resource in storage on file upload.
     */
    public function storeOnUpload(JsonFileUploadRequest $request, $resourceName): JsonResponse
    {
        try {
            $file = $request->file('files');
            $json = $file->get();

            // get the model class using the singular version of the resource name
            $modelClass = '\\App\\Models\\' . Str::studly(Str::singular($resourceName));

            // validate the json data against the json schema
            $result = (new Validator())->validate(json_decode($json), json_decode($modelClass::$schema));
            if (!$result->isValid()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The uploaded JSON files are incompatible with the ' . Str::singular($resourceName) . ' JSON schema.<br><br>' . implode('<br>', (new ErrorFormatter())->formatFlat($result->error())),
                ], 400);
            }
    
            // decode the json file
            $data = json_decode($json, true);

            // populate the fillable fields for the resource
            $instance = new $modelClass();
            $fields = $instance->getFillableFields($data, $json);

            // create the resource
            $resource = $modelClass::create($fields);

            return response()->json([
                'status' => $resource ? 'success' : 'error',
                'message' => $resource ? 'The JSON file has been successfully uploaded.' : 'Error uploading the JSON file.',
                'resourceId' => basename($data['ark']),
            ], $resource ? 200 : 400);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'The uploaded JSON files are incompatible with the ' . Str::singular($resourceName) . ' JSON schema.<br><br>' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage on file upload.
     */
    public function updateOnUpload(JsonFileUploadRequest $request, $resourceName, $resourceId): JsonResponse
    {
        try {
            $file = $request->file('files');
            $json = $file->get();

            // get the model class using the singular version of the resource name
            $modelClass = '\\App\\Models\\' . Str::studly(Str::singular($resourceName));

            // validate the json data against the json schema
            $result = (new Validator())->validate(json_decode($json), json_decode($modelClass::$schema));
            if (!$result->isValid()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The uploaded JSON files are incompatible with the ' . Str::singular($resourceName) . ' JSON schema.<br><br>' . implode('<br>', (new ErrorFormatter())->formatFlat($result->error())),
                ], 400);
            }

            // get the resource with the given id
            $resource = $modelClass::find($resourceId);

            // decode the json file
            $data = json_decode($json, true);

            // populate the fillable fields for the resource
            $fields = $resource->getFillableFields($data, $json);

            // update the resource
            $status = $resource->update($fields);

            return response()->json([
                'status' => $status ? 'success' : 'error',
                'message' => $status ? 'The JSON file has been successfully uploaded.' : 'Error uploading the JSON file.',
            ], $status ? 200 : 400);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'The uploaded JSON files are incompatible with the ' . Str::singular($resourceName) . ' JSON schema.<br><br>' . $e->getMessage(),
            ], 400);
        }
    }

    public function batchUpload(JsonBatchUploadRequest $request, $resourceName): JsonResponse
    {
        try {
            $messages = [];

            $files = $request->file('files');
            foreach ($files as $file) {
                $json = $file->get();

                // get the model class using the singular version of the resource name
                $resourceType = Str::studly(Str::singular($resourceName));
                $modelClass = '\\App\\Models\\' . $resourceType;

                // validate the json data against the json schema
                $result = (new Validator())->validate(json_decode($json), json_decode($modelClass::$schema));
                if (!$result->isValid()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The uploaded JSON files are incompatible with the ' . Str::singular($resourceName) . ' JSON schema.<br><br>' . implode('<br>', (new ErrorFormatter())->formatFlat($result->error())),
                    ], 400);
                }

                // decode the json file
                $data = json_decode($json, true);

                // populate the fillable fields for the resource
                $instance = new $modelClass();
                $fields = $instance->getFillableFields($data, $json);

                // get the resource with the given id
                $resourceId = basename($fields['id']);
                $resource = $modelClass::find($resourceId);

                if ($resource) {
                    // update the resource
                    if ($resource->update($fields)) {
                        $messages[] = $resourceType . " with '" . $data['ark'] . "' has been updated.";
                    }
                }
                else {
                    // create the resource
                    if ($modelClass::create($fields)) {
                        $messages[] = $resourceType . " with '" . $data['ark'] . "' has been created.";
                        $createdResources[$resourceId] = $data['ark'];
                    }
                }
            }

            return response()->json([
                'status' => count($messages) > 0 ? 'success' : 'error',
                'message' => count($messages) > 0 ? implode('<br>', $messages) : 'Error uploading JSON file(s).',
            ], count($messages) > 0 ? 200 : 400);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'The uploaded JSON files are incompatible with the ' . Str::singular($resourceName) . ' JSON schema.<br><br>' . $e->getMessage(),
            ], 400);
        }
    }
}
