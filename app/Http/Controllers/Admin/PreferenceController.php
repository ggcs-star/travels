<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AllowedFileExtensionsService;
use App\Services\Payments\PaymentSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    public function __construct(
        protected AllowedFileExtensionsService $extensions,
        protected PaymentSettingsService $payments,
    ) {
    }

    /**
     * Preferences dashboard.
     */
    public function index(): View
    {
        $enabledExtensions = $this->extensions->get();

        $availableExtensions = $this->extensions->getAvailable();

        return view(
            'admin.settings.preferences',
            [
                'extensions' => $enabledExtensions,

                'allExtensions' => $availableExtensions,

                'blockedExtensions' =>
                    AllowedFileExtensionsService::BLOCKED_EXTENSIONS,

                'razorpay' => [
                    'key_id' =>
                        $this->payments->getKeyId(),

                    'base_url' =>
                        $this->payments->getBaseUrl(),

                    'enabled' =>
                        $this->payments->isEnabled(),

                    'has_key_secret' =>
                        filled(
                            $this->payments->getKeySecret()
                        ),

                    'has_webhook_secret' =>
                        filled(
                            $this->payments->getWebhookSecret()
                        ),
                ],
            ]
        );
    }

    /**
     * Save upload preferences.
     */
    public function updateUploads(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'allowed_extensions' => [
                'nullable',
                'array',
                'max:100',
            ],

            'allowed_extensions.*' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9.]+$/',
            ],
        ]);

        $saved = $this->extensions->save(
            $validated['allowed_extensions'] ?? []
        );

        return redirect()
            ->route(
                'admin.settings.preferences'
            )
            ->with(
                'success',
                count($saved)
                    . ' allowed file extension(s) saved successfully.'
            );
    }

    /**
     * Save Razorpay preferences.
     */
    public function updatePayments(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'razorpay_key_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'razorpay_key_secret' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'razorpay_webhook_secret' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'razorpay_base_url' => [
                'required',
                'url:http,https',
                'max:255',
            ],

            'razorpay_enabled' => [
                'nullable',
                'boolean',
            ],

            'clear_razorpay_key_id' => [
                'nullable',
                'boolean',
            ],

            'clear_razorpay_key_secret' => [
                'nullable',
                'boolean',
            ],

            'clear_razorpay_webhook_secret' => [
                'nullable',
                'boolean',
            ],
        ]);

        $clear = [];

        foreach (
            [
                'key_id',
                'key_secret',
                'webhook_secret',
            ] as $field
        ) {
            if (
                $request->boolean(
                    'clear_razorpay_' . $field
                )
            ) {
                $clear[] = $field;
            }
        }

        $this->payments->save(
            [
                'key_id' =>
                    $validated['razorpay_key_id']
                    ?? null,

                'key_secret' =>
                    $validated['razorpay_key_secret']
                    ?? null,

                'webhook_secret' =>
                    $validated['razorpay_webhook_secret']
                    ?? null,

                'base_url' =>
                    $validated['razorpay_base_url'],

                'enabled' =>
                    $request->boolean(
                        'razorpay_enabled'
                    ),
            ],
            $clear
        );

        return redirect()
            ->route(
                'admin.settings.preferences'
            )
            ->with(
                'success',
                'Razorpay settings saved successfully.'
            );
    }

    /**
     * Test Razorpay connection.
     */
    public function testPayments(): RedirectResponse
    {
        try {
            $result = app(
                \App\Services\Payments\RazorpayService::class
            )->testConnection();

            return redirect()
                ->route(
                    'admin.settings.preferences'
                )
                ->with(
                    'success',
                    $result['message']
                );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route(
                    'admin.settings.preferences'
                )
                ->with(
                    'error',
                    'Razorpay connection test failed. Please verify the saved Key ID, Key Secret and gateway status.'
                );
        }
    }

    /**
     * Restore default upload extensions.
     */
    public function reset(): RedirectResponse
    {
        $this->extensions->save(
            AllowedFileExtensionsService::DEFAULT_EXTENSIONS
        );

        return redirect()
            ->route(
                'admin.settings.preferences'
            )
            ->with(
                'success',
                'Default upload extensions have been restored.'
            );
    }
}