<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourDepartureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'price' => ['required', 'decimal:0,2', 'min:0'],
            'sale_price' => ['nullable', 'decimal:0,2', 'min:0', 'lt:price'],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'meeting_point' => ['nullable', 'string', 'max:255'],
            'status' => [
                'required',
                Rule::in(['open', 'closed', 'cancelled']),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => strtoupper((string) $this->input('currency', 'INR')),
        ]);
    }
}
