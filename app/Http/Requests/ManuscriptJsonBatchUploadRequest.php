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
