<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && (
                $user->isAdmin()
                || $user->isSuperAdmin()
            );
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */

            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Departure
            |--------------------------------------------------------------------------
            */

            'departure_id' => [
                'required',
                'integer',
                'exists:tour_departures,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Contact Details
            |--------------------------------------------------------------------------
            */

            'contact_name' => [
                'required',
                'string',
                'max:150',
            ],

            'contact_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],

            'contact_phone' => [
                'required',
                'string',
                'max:40',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'special_requests' => [
                'nullable',
                'string',
                'max:2000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Travellers
            |--------------------------------------------------------------------------
            |
            | The actual traveller count is determined server-side using
            | count($data['travellers']). The frontend count is never trusted.
            */

            'travellers' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'travellers.*.full_name' => [
                'required',
                'string',
                'max:150',
            ],

            'travellers.*.email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
            ],

            'travellers.*.phone' => [
                'nullable',
                'string',
                'max:40',
            ],

            'travellers.*.date_of_birth' => [
                'required',
                'date',
                'before:today',
            ],

            'travellers.*.gender' => [
                'required',
                Rule::in([
                    'female',
                    'male',
                    'non_binary',
                    'prefer_not_to_say',
                ]),
            ],

            'travellers.*.id_proof_type' => [
                'required',
                'string',
                Rule::in([
                    'aadhaar',
                    'passport',
                    'driving_license',
                    'voter_id',
                    'pan_card',
                    'other',
                ]),
            ],

            'travellers.*.id_proof_number' => [
                'required',
                'string',
                'max:100',
            ],

            'travellers.*.id_proof_document' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'user_id.required' =>
                'Please select the customer this booking is for.',

            'user_id.exists' =>
                'The selected customer could not be found.',

            'departure_id.required' =>
                'Please select a departure date.',

            'departure_id.exists' =>
                'The selected departure is invalid.',

            'contact_name.required' =>
                'Contact name is required.',

            'contact_email.required' =>
                'Contact email is required.',

            'contact_phone.required' =>
                'Contact phone number is required.',

            'travellers.required' =>
                'Please add at least one traveller.',

            'travellers.min' =>
                'At least one traveller is required.',

            'travellers.max' =>
                'A maximum of 10 travellers can be added per booking.',

            'travellers.*.full_name.required' =>
                'Full name is required for every traveller.',

            'travellers.*.date_of_birth.required' =>
                'Date of birth is required for every traveller.',

            'travellers.*.gender.required' =>
                'Please select gender for every traveller.',

            'travellers.*.id_proof_type.required' =>
                'ID proof type is required for every traveller.',

            'travellers.*.id_proof_number.required' =>
                'ID proof number is required for every traveller.',

            'travellers.*.id_proof_document.required' =>
                'ID proof document is required for every traveller.',

            'travellers.*.id_proof_document.mimes' =>
                'ID proof must be JPG, JPEG, PNG, WEBP, or PDF.',

            'travellers.*.id_proof_document.max' =>
                'Each ID proof document may not be larger than 5 MB.',
        ];
    }

    /**
     * Human-readable attribute names.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'customer',
            'departure_id' => 'departure',
            'contact_name' => 'contact name',
            'contact_email' => 'contact email',
            'contact_phone' => 'contact phone',
            'travellers.*.full_name' => 'traveller full name',
            'travellers.*.date_of_birth' => 'traveller date of birth',
            'travellers.*.gender' => 'traveller gender',
            'travellers.*.id_proof_type' => 'traveller ID proof type',
            'travellers.*.id_proof_number' => 'traveller ID proof number',
            'travellers.*.id_proof_document' => 'traveller ID proof document',
        ];
    }
}
