<?php

namespace App\Http\Requests\Admin;

use App\Models\PointSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePointSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin()
            || $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'settings' => [
                'required',
                'array',
            ],

            'settings.*.enabled' => [
                'nullable',
                'boolean',
            ],

            'settings.*.reward_type' => [
                'nullable',
                Rule::in([
                    PointSetting::REWARD_FIXED,
                    PointSetting::REWARD_AMOUNT_BASED,
                ]),
            ],

            'settings.*.calculation_basis' => [
                'nullable',
                Rule::in([
                    PointSetting::BASIS_BOOKING_TOTAL,
                    PointSetting::BASIS_FINAL_PAID,
                ]),
            ],

            'settings.*.points' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'settings.*.minimum_amount' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'settings.*.amount_unit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'settings.*.redemption_enabled' => [
                'nullable',
                'boolean',
            ],

            'settings.*.point_value' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'settings.*.max_redemption_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'settings.*.max_points_per_booking' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'settings.*.expiry_enabled' => [
                'nullable',
                'boolean',
            ],

            'settings.*.expiry_days' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $settings = $this->input('settings', []);

        foreach ($settings as $key => &$setting) {
            $setting['enabled'] =
                isset($setting['enabled']);

            $setting['redemption_enabled'] =
                isset($setting['redemption_enabled']);

            $setting['expiry_enabled'] =
                isset($setting['expiry_enabled']);
        }

        $this->merge([
            'settings' => $settings,
        ]);
    }
}