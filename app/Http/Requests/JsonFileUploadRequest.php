<?php

namespace App\Http\Requests;

// use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
// use Swaggest\JsonSchema\Schema;

class JsonFileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => [
                'required',                     // ensure the field is not empty
                'file',                         // ensure the field is a valid file
                'mimes:json',                   // ensure the file contents match the mime type from the file extension
                'mimetypes:application/json',   // ensure the file contents match the explicit mime type
            ],
        ];
    }

    /**
     * Handle additional validation after the base rules have passed.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('files')) {
                $file = $this->file('files');

                // ensure the file has a ".json" extension
                if ($file->getClientOriginalExtension() !== 'json') {
                    $validator->errors()->add('files', 'The file must have a .json extension.');
                    return;
                }

                // decode the json file
                $json = json_decode($file->get());

                // ensure the json is valid
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $validator->errors()->add('files', 'The file must contain valid JSON.');
                    return;
                }

                // get the model class using the singular version of the resource name
                $modelClass = '\\App\\Models\\' . ucfirst(Str::singular($this->route('resourceName')));

                // ensure the resource has a valid json schema
                if (!class_exists($modelClass)) {
                    $validator->errors()->add('files', 'Invalid resource type.');
                    return;
                }
                if (!property_exists($modelClass, 'schema')) {
                    $validator->errors()->add('files', 'JSON schema is not defined.');
                    return;
                }

                // TODO: uncomment this when the json files for each resource type aligns with their corresponding json schema
                // ensure the json validates against json schema
                // try {
                //     Schema::import(json_decode($modelClass::$schema))->in($json);
                // }
                // catch (Exception $e) {
                //     $validator->errors()->add('files', 'JSON schema validation error: ' . $e->getMessage());
                //     return;
                // }

                // decode the json file
                $data = json_decode($file->get(), true);

                // on update, ensure the ark in the json file matches the ark of the resource
                $resourceId = $this->route('resourceId');
                if ($resourceId) {
                    $resource = $modelClass::find($resourceId);
                    if ($data['ark'] !== $resource->ark) {
                        $validator->errors()->add('ark', 'The "ark" in the JSON file must match the "ark" of this record.');
                        return;
                    }
                }
                // on store, ensure the ark in the json file is not used by an existing resource
                else {
                    if ($modelClass::where('ark', $data['ark'])->exists()) {
                        $validator->errors()->add('ark', 'A record with this "ark" already exists.');
                        return;
                    }
                }
            }
        });
    }
}
