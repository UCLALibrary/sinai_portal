<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManuscriptJsonBatchUploadRequest extends FormRequest
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
                    $jsonData = json_decode($file->get(), true);
    
                    // ensure the json is valid
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $validator->errors()->add('files', 'The file must contain valid JSON.');
                        return;
                    }
    
                    // ensure the "ark" field is not empty
                    $ark = data_get($jsonData, 'ark');
                    if (empty($ark)) {
                        $validator->errors()->add('ark', 'The "ark" field is required and cannot be empty.');
                        return;
                    }
    
                    // ensure the "type/label" field is not empty
                    if (empty(data_get($jsonData, 'type.label'))) {
                        $validator->errors()->add('type.label', 'The "type/label" field is required and cannot be empty.');
                        return;
                    }
    
                    // ensure the "shelfmark" field is not empty
                    if (empty(data_get($jsonData, 'shelfmark'))) {
                        $validator->errors()->add('shelfmark', 'The "shelfmark" field is required and cannot be empty.');
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
