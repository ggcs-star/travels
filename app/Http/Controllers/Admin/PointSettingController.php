<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePointSettingsRequest;
use App\Models\PointSetting;
use App\Services\Points\PointSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PointSettingController extends Controller
{
    public function __construct(
        private readonly PointSettingService $pointSettingService
    ) {}

    public function index(): View
    {
        $settings = PointSetting::query()
            ->orderBy('id')
            ->get()
            ->keyBy('key');

        return view(
            'admin.point-settings.index',
            compact('settings')
        );
    }

    public function update(
        UpdatePointSettingsRequest $request
    ): RedirectResponse {
        foreach ($request->validated('settings') as $key => $data) {
            $setting = PointSetting::query()
                ->where('key', $key)
                ->first();

            if (! $setting) {
                continue;
            }

            $this->pointSettingService->update(
                $key,
                [
                    'name' => $setting->name,
                    'description' => $setting->description,

                    'enabled' =>
                        $data['enabled'] ?? false,

                    'reward_type' =>
                        $data['reward_type'] ?? $setting->reward_type,

                    'calculation_basis' =>
                        $data['calculation_basis']
                        ?? $setting->calculation_basis,

                    'points' =>
                        $data['points'] ?? 0,

                    'minimum_amount' =>
                        $data['minimum_amount'] ?? null,

                    'amount_unit' =>
                        $data['amount_unit'] ?? null,

                    'redemption_enabled' =>
                        $data['redemption_enabled'] ?? false,

                    'point_value' =>
                        $data['point_value'] ?? null,

                    'max_redemption_percent' =>
                        $data['max_redemption_percent'] ?? null,

                    'max_points_per_booking' =>
                        $data['max_points_per_booking'] ?? null,

                    'expiry_enabled' =>
                        $data['expiry_enabled'] ?? false,

                    'expiry_days' =>
                        $data['expiry_days'] ?? null,
                ]
            );
        }

        return back()->with(
            'success',
            'Points settings updated successfully.'
        );
    }
}