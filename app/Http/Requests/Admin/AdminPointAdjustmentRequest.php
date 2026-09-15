<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminPointAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin()
            || $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'action' => [
                'required',
                Rule::in([
                    'add',
                    'deduct',
                ]),
            ],

            'points' => [
                'required',
                'integer',
                'min:1',
                'max:1000000000',
            ],

            'reason' => [
                'required',
                'string',
                'min:3',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Please select an adjustment type.',
            'action.in' => 'Invalid adjustment type.',

            'points.required' => 'Please enter the points.',
            'points.integer' => 'Points must be a whole number.',
            'points.min' => 'Points must be at least 1.',
            'points.max' => 'The points amount is too large.',

            'reason.required' => 'A reason is required.',
            'reason.min' => 'The reason must contain at least 3 characters.',
            'reason.max' => 'The reason cannot exceed 500 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'action' => strtolower(
                trim((string) $this->input('action'))
            ),

            'points' => is_numeric($this->input('points'))
                ? (int) $this->input('points')
                : $this->input('points'),

            'reason' => trim(
                (string) $this->input('reason')
            ),
        ]);
    }
}