<?php

namespace App\Services\Payments;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Crypt;

class PaymentSettingsService
{
    public const KEY_ID = 'payments.razorpay.key_id';

    public const KEY_SECRET = 'payments.razorpay.key_secret';

    public const WEBHOOK_SECRET = 'payments.razorpay.webhook_secret';

    public const BASE_URL = 'payments.razorpay.base_url';

    public const ENABLED = 'payments.razorpay.enabled';

    /**
     * Get Razorpay Key ID.
     */
    public function getKeyId(): ?string
    {
        return $this->getSecret(self::KEY_ID);
    }

    /**
     * Get Razorpay Key Secret.
     */
    public function getKeySecret(): ?string
    {
        return $this->getSecret(self::KEY_SECRET);
    }

    /**
     * Get Razorpay Webhook Secret.
     */
    public function getWebhookSecret(): ?string
    {
        return $this->getSecret(self::WEBHOOK_SECRET);
    }

    /**
     * Get canonical Razorpay API base URL.
     *
     * The RazorpayService appends /v1 itself.
     *
     * Stored/configured values such as:
     *
     * https://api.razorpay.com
     * https://api.razorpay.com/
     * https://api.razorpay.com/v1
     * https://api.razorpay.com/v1/
     *
     * are all normalized to:
     *
     * https://api.razorpay.com
     */
    public function getBaseUrl(): string
    {
        $baseUrl = (string) $this->get(
            self::BASE_URL,
            'https://api.razorpay.com'
        );

        return $this->normalizeBaseUrl($baseUrl);
    }

    /**
     * Check whether Razorpay is enabled.
     */
    public function isEnabled(): bool
    {
        return (bool) $this->get(
            self::ENABLED,
            false
        );
    }

    /**
     * Check complete Razorpay configuration.
     */
    public function isConfigured(): bool
    {
        return $this->isEnabled()
            && filled($this->getKeyId())
            && filled($this->getKeySecret());
    }

    /**
     * Save Razorpay configuration.
     *
     * Sensitive credentials are encrypted before storage.
     */
    public function save(
        array $data,
        array $clear = []
    ): void {
        /*
         * Key ID
         */
        if (
            in_array(
                'key_id',
                $clear,
                true
            )
        ) {
            SiteSetting::query()
                ->where(
                    'key',
                    self::KEY_ID
                )
                ->delete();
        } elseif (
            array_key_exists(
                'key_id',
                $data
            )
            && filled($data['key_id'])
        ) {
            $this->saveValue(
                self::KEY_ID,
                trim(
                    (string) $data['key_id']
                ),
                true
            );
        }

        /*
         * Base URL
         *
         * Keep only the API host/base path here.
         * RazorpayService adds /v1.
         */
        $baseUrl = $this->normalizeBaseUrl(
            (string) (
                $data['base_url']
                ?? 'https://api.razorpay.com'
            )
        );

        $this->saveValue(
            self::BASE_URL,
            $baseUrl,
            false,
            false
        );

        /*
         * Enabled
         */
        $this->saveValue(
            self::ENABLED,
            ! empty($data['enabled'])
                ? '1'
                : '0',
            false,
            false
        );

        /*
         * Secrets
         */
        foreach (
            [
                'key_secret' => self::KEY_SECRET,
                'webhook_secret' => self::WEBHOOK_SECRET,
            ] as $field => $key
        ) {
            /*
             * Explicit clear
             */
            if (
                in_array(
                    $field,
                    $clear,
                    true
                )
            ) {
                SiteSetting::query()
                    ->where(
                        'key',
                        $key
                    )
                    ->delete();

                continue;
            }

            /*
             * Empty secret means:
             *
             * "Keep existing value."
             */
            if (
                array_key_exists(
                    $field,
                    $data
                )
                && filled($data[$field])
            ) {
                $this->saveValue(
                    $key,
                    trim(
                        (string) $data[$field]
                    ),
                    true
                );
            }
        }
    }

    /**
     * Read setting.
     */
    private function get(
        string $key,
        mixed $default = null
    ): mixed {
        $setting = SiteSetting::query()
            ->where(
                'key',
                $key
            )
            ->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->value;

        /*
         * Sensitive Razorpay values are encrypted.
         */
        if (
            $key !== self::ENABLED
            && $key !== self::BASE_URL
        ) {
            try {
                $value = Crypt::decryptString(
                    (string) $value
                );
            } catch (\Throwable) {
                /*
                 * Backward compatibility:
                 *
                 * Older installations may have stored
                 * the Key ID as plaintext.
                 *
                 * Never fallback to plaintext for secrets.
                 */
                if (
                    $key !== self::KEY_ID
                ) {
                    return null;
                }
            }
        }

        if (
            $key === self::ENABLED
        ) {
            return filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        return $value;
    }

    /**
     * Return secret-like value.
     */
    private function getSecret(
        string $key
    ): ?string {
        $value = $this->get($key);

        return filled($value)
            ? (string) $value
            : null;
    }

    /**
     * Save a setting.
     */
    private function saveValue(
        string $key,
        mixed $value,
        bool $encrypt,
        bool $skipEmpty = true
    ): void {
        if (
            $skipEmpty
            && ! filled($value)
        ) {
            return;
        }

        if ($encrypt) {
            $value = Crypt::encryptString(
                (string) $value
            );
        }

        SiteSetting::updateOrCreate(
            [
                'key' => $key,
            ],
            [
                'value' => (string) $value,

                'type' =>
                    $key === self::ENABLED
                        ? 'boolean'
                        : 'text',

                'group' => 'payments',

                'is_public' => false,
            ]
        );
    }

    /**
     * Normalize the configured Razorpay base URL.
     *
     * We intentionally remove /v1 because the API client
     * adds /v1 exactly once.
     */
    private function normalizeBaseUrl(
        string $baseUrl
    ): string {
        $baseUrl = trim($baseUrl);

        if ($baseUrl === '') {
            return 'https://api.razorpay.com';
        }

        $baseUrl = rtrim(
            $baseUrl,
            '/'
        );

        /*
         * Remove any trailing /v1 so that the final
         * API URL can never become /v1/v1.
         */
        $baseUrl = preg_replace(
            '#/v1$#i',
            '',
            $baseUrl
        );

        return rtrim(
            (string) $baseUrl,
            '/'
        );
    }
}