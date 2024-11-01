<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class JsonBatchUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('uploadRecord', User::class);
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
