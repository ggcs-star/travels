<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AllowedFileExtensionsService
{
    /**
     * All standard extensions available in the Preferences UI.
     *
     * These are only available options.
     * They are NOT automatically enabled after an admin saves
     * an empty configuration.
     */
    public const DEFAULT_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
        'svg',
        'ico',

        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'csv',
        'txt',

        'ppt',
        'pptx',
        'psd',
        'zip',

        'mp4',
        'mp3',
    ];

    /**
     * Extensions that can NEVER be enabled.
     */
    public const BLOCKED_EXTENSIONS = [
        'php',
        'php3',
        'php4',
        'php5',
        'php7',
        'php8',
        'phtml',
        'phar',

        'cgi',
        'pl',
        'py',
        'rb',
        'sh',
        'bash',
        'zsh',
        'fish',

        'htaccess',
        'htpasswd',
        'env',
        'ini',
        'conf',
        'config',
    ];

    public function __construct(
        protected SettingsService $settings,
    ) {
    }

    /**
     * Get the currently ENABLED extensions.
     *
     * IMPORTANT:
     * [] means no upload types are allowed.
     */
    public function get(): array
    {
        $configured = $this->settings->get(
            'uploads.allowed_extensions',
            null
        );

        /*
         * Setting does not exist yet.
         * On a fresh installation use defaults.
         */
        if ($configured === null) {
            return self::DEFAULT_EXTENSIONS;
        }

        if (is_string($configured)) {
            $decoded = json_decode($configured, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $configured = $decoded;
            }
        }

        /*
         * If the setting exists and is an empty array,
         * that means ALL uploads are disabled.
         */
        if (! is_array($configured)) {
            return [];
        }

        return $this->normalize($configured);
    }

    /**
     * Get all extensions which should be displayed in the UI.
     *
     * This includes:
     * - standard extensions
     * - custom configured extensions
     *
     * But NEVER blocked extensions.
     */
    public function getAvailable(): array
    {
        $configured = $this->get();

        $available = array_merge(
            self::DEFAULT_EXTENSIONS,
            $configured
        );

        $available = $this->normalize($available);

        /*
         * Keep standard options first, then custom extensions.
         */
        $standard = [];

        foreach (self::DEFAULT_EXTENSIONS as $extension) {
            if (in_array($extension, $available, true)) {
                $standard[] = $extension;
            }
        }

        $custom = array_values(
            array_diff($available, self::DEFAULT_EXTENSIONS)
        );

        sort($custom);

        return array_values(
            array_unique(
                array_merge($standard, $custom)
            )
        );
    }

    /**
     * Save allowed extensions.
     *
     * Empty array is intentionally stored as [].
     */
    public function save(array $extensions): array
    {
        $normalized = [];

        foreach ($extensions as $extension) {
            $extension = $this->normalizeOne($extension);

            if ($extension === '') {
                continue;
            }

            if (! preg_match('/^[a-z0-9]{1,20}$/', $extension)) {
                throw ValidationException::withMessages([
                    'allowed_extensions' =>
                        "Invalid file extension: {$extension}.",
                ]);
            }

            if (
                in_array(
                    $extension,
                    self::BLOCKED_EXTENSIONS,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'allowed_extensions' =>
                        "The .{$extension} extension cannot be enabled for security reasons.",
                ]);
            }

            $normalized[] = $extension;
        }

        $normalized = array_values(
            array_unique($normalized)
        );

        sort($normalized);

        /*
         * DO NOT replace [] with DEFAULT_EXTENSIONS.
         *
         * [] = no uploads allowed.
         */
        $this->settings->set(
            key: 'uploads.allowed_extensions',
            value: $normalized,
            group: 'preferences',
            type: 'json',
            isPublic: false,
        );

        return $normalized;
    }

    /**
     * Check one uploaded file.
     */
    public function accepts(UploadedFile $file): bool
    {
        if (! $file->isValid()) {
            return false;
        }

        $extension = Str::lower(
            trim(
                $file->getClientOriginalExtension()
            )
        );

        return in_array(
            $extension,
            $this->get(),
            true
        );
    }

    /**
     * Validate all uploaded files recursively.
     */
    public function validateFiles(array $files): void
    {
        foreach ($files as $file) {
            if (is_array($file)) {
                $this->validateFiles($file);
                continue;
            }

            if (! $file instanceof UploadedFile) {
                continue;
            }

            /*
             * Let Laravel handle invalid upload errors.
             */
            if (! $file->isValid()) {
                continue;
            }

            $extension = Str::lower(
                trim(
                    $file->getClientOriginalExtension()
                )
            );

            $allowed = $this->get();

            /*
             * Empty allowed list means EVERYTHING is rejected.
             */
            if (
                empty($allowed)
                || ! in_array(
                    $extension,
                    $allowed,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'file' =>
                        "Files with the .{$extension} extension are not allowed. Please upload an allowed file type.",
                ]);
            }
        }
    }

    /**
     * Normalize complete extension array.
     */
    private function normalize(array $extensions): array
    {
        $normalized = [];

        foreach ($extensions as $extension) {
            $extension = $this->normalizeOne($extension);

            if ($extension === '') {
                continue;
            }

            if (! preg_match('/^[a-z0-9]{1,20}$/', $extension)) {
                continue;
            }

            if (
                in_array(
                    $extension,
                    self::BLOCKED_EXTENSIONS,
                    true
                )
            ) {
                continue;
            }

            $normalized[] = $extension;
        }

        $normalized = array_values(
            array_unique($normalized)
        );

        sort($normalized);

        return $normalized;
    }

    /**
     * Normalize a single extension.
     */
    private function normalizeOne(mixed $extension): string
    {
        $extension = Str::lower(
            trim((string) $extension)
        );

        return ltrim($extension, '.');
    }
}