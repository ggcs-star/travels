<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected string $cacheKey = 'travels.site_settings';

    /**
     * Get all public settings.
     */
    public function all(): array
    {
        return Cache::remember(
            $this->cacheKey,
            now()->addHours(24),
            function () {
                return SiteSetting::query()
                    ->where('is_public', true)
                    ->get()
                    ->mapWithKeys(function (SiteSetting $setting) {
                        return [
                            $setting->key => $this->castValue(
                                $setting->value,
                                $setting->type
                            ),
                        ];
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Get a single setting.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    /**
     * Create or update a setting.
     */
    public function set(
        string $key,
        mixed $value,
        string $group = 'general',
        string $type = 'text',
        bool $isPublic = true
    ): SiteSetting {
        $setting = SiteSetting::updateOrCreate(
            [
                'key' => $key,
            ],
            [
                'value' => $this->prepareValue($value, $type),
                'type' => $type,
                'group' => $group,
                'is_public' => $isPublic,
            ]
        );

        $this->clearCache();

        return $setting;
    }

    /**
     * Save multiple settings.
     */
    public function setMany(array $settings): void
    {
        foreach ($settings as $key => $data) {
            if (is_array($data)) {
                $this->set(
                    key: $key,
                    value: $data['value'] ?? null,
                    group: $data['group'] ?? 'general',
                    type: $data['type'] ?? 'text',
                    isPublic: $data['is_public'] ?? true,
                );
            } else {
                $this->set(
                    key: $key,
                    value: $data,
                );
            }
        }

        $this->clearCache();
    }

    /**
     * Clear settings cache.
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Convert database value to PHP value.
     */
    protected function castValue(
        mixed $value,
        string $type
    ): mixed {
        return match ($type) {
            'boolean' => filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN
            ),

            'integer' => (int) $value,

            'float' => (float) $value,

            'json' => json_decode(
                $value ?? '[]',
                true
            ) ?? [],

            default => $value,
        };
    }

    /**
     * Convert PHP value before saving.
     */
    protected function prepareValue(
        mixed $value,
        string $type
    ): ?string {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',

            'json' => json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
            ),

            default => (string) $value,
        };
    }
}