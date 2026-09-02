<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
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
            |
            */

            'travellers' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            /*
            |--------------------------------------------------------------------------
            | Traveller Personal Details
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Identity Proof
            |--------------------------------------------------------------------------
            |
            | Every traveller must provide their own identity proof.
            |
            */

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
            /*
            |--------------------------------------------------------------------------
            | Departure
            |--------------------------------------------------------------------------
            */

            'departure_id.required' =>
                'Please select a departure date.',

            'departure_id.exists' =>
                'The selected departure is invalid.',

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            'contact_name.required' =>
                'Contact name is required.',

            'contact_email.required' =>
                'Contact email is required.',

            'contact_email.email' =>
                'Please enter a valid contact email address.',

            'contact_phone.required' =>
                'Contact phone number is required.',

            /*
            |--------------------------------------------------------------------------
            | Travellers
            |--------------------------------------------------------------------------
            */

            'travellers.required' =>
                'Please add at least one traveller.',

            'travellers.array' =>
                'Invalid traveller details.',

            'travellers.min' =>
                'At least one traveller is required.',

            'travellers.max' =>
                'A maximum of 10 travellers can be added per booking.',

            /*
            |--------------------------------------------------------------------------
            | Traveller Personal Details
            |--------------------------------------------------------------------------
            */

            'travellers.*.full_name.required' =>
                'Full name is required for every traveller.',

            'travellers.*.full_name.max' =>
                'Traveller name may not be longer than 150 characters.',

            'travellers.*.email.email' =>
                'Please enter a valid email address.',

            'travellers.*.date_of_birth.required' =>
                'Date of birth is required for every traveller.',

            'travellers.*.date_of_birth.date' =>
                'Please enter a valid date of birth.',

            'travellers.*.date_of_birth.before' =>
                'Date of birth must be before today.',

            'travellers.*.gender.required' =>
                'Please select gender for every traveller.',

            'travellers.*.gender.in' =>
                'Please select a valid gender option.',

            /*
            |--------------------------------------------------------------------------
            | ID Proof
            |--------------------------------------------------------------------------
            */

            'travellers.*.id_proof_type.required' =>
                'ID proof type is required for every traveller.',

            'travellers.*.id_proof_type.in' =>
                'Please select a valid ID proof type.',

            'travellers.*.id_proof_number.required' =>
                'ID proof number is required for every traveller.',

            'travellers.*.id_proof_number.max' =>
                'ID proof number may not be longer than 100 characters.',

            'travellers.*.id_proof_document.required' =>
                'ID proof document is required for every traveller.',

            'travellers.*.id_proof_document.file' =>
                'Each ID proof must be a valid file.',

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
            'departure_id' =>
                'departure',

            'contact_name' =>
                'contact name',

            'contact_email' =>
                'contact email',

            'contact_phone' =>
                'contact phone',

            'special_requests' =>
                'special requests',

            'travellers' =>
                'travellers',

            'travellers.*.full_name' =>
                'traveller full name',

            'travellers.*.email' =>
                'traveller email',

            'travellers.*.phone' =>
                'traveller phone',

            'travellers.*.date_of_birth' =>
                'traveller date of birth',

            'travellers.*.gender' =>
                'traveller gender',

            'travellers.*.id_proof_type' =>
                'traveller ID proof type',

            'travellers.*.id_proof_number' =>
                'traveller ID proof number',

            'travellers.*.id_proof_document' =>
                'traveller ID proof document',
        ];
    }
}