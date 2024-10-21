<?php

namespace App\Http\Requests;

// use Exception;
use Illuminate\Foundation\Http\FormRequest;
// use Swaggest\JsonSchema\Schema;

class JsonBatchUploadRequest extends FormRequest
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
                'required',  // ensure the field is not empty
                'array',     // ensure the field is an array
            ],

            'files.*' => [
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
                foreach ($this->file('files') as $file) {
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

                    // ensure the resource has a valid json schema
                    $modelClass = '\\App\\Models\\' . ucfirst($this->route('resourceType'));
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
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        if ($this->hasFile('files')) {
            foreach ($this->file('files') as $key => $file) {
                $attributes['files.' . $key] = $file->getClientOriginalName();
            }
        }

        return $attributes;
    }
}
