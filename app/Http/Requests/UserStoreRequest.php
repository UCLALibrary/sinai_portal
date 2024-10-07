<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: check if the user is authorized to make this request
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function myRules(): array
    {
        return [
            'name' => [
                'required',
                'max:50'
            ],
            'email' => [
                'required',
                'max:50',
                'email',
                Rule::unique('users')
            ],
            'password' => [
                'nullable'
            ],
			'role' => [
				'required',
				'integer'
			]
        ];
    }
}
