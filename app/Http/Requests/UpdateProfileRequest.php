<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | User Account
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | User Profile
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'nullable',
                'string',
                'max:40',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            'gender' => [
                'nullable',
                'in:male,female,other,prefer_not_to_say',
            ],

            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => filled($this->name)
                ? trim($this->name)
                : $this->name,

            'phone' => filled($this->phone)
                ? trim($this->phone)
                : null,

            'city' => filled($this->city)
                ? trim($this->city)
                : null,

            'state' => filled($this->state)
                ? trim($this->state)
                : null,

            'country' => filled($this->country)
                ? trim($this->country)
                : null,

            'postal_code' => filled($this->postal_code)
                ? trim($this->postal_code)
                : null,
        ]);
    }
}