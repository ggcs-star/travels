<?php

namespace App\Http\Controllers\Api\V1;

use App\Mail\PasswordResetOtpMail;
use App\Models\ApiToken;
use App\Models\PasswordResetOtp;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    private const OTP_EXPIRES_MINUTES = 10;
    private const MAX_OTP_ATTEMPTS = 5;
    private const RESET_TOKEN_MINUTES = 15;

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'username' => [
                'required',
                'string',
                'min:4',
                'max:100',
                'regex:/^[A-Za-z0-9_.-]+$/',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:200',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:4',
                'max:200',
                'confirmed',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'status' => true,
        ]);

        event(new Registered($user));

        return $this->issueToken(
            $user,
            $data['device_name'] ?? 'mobile-app',
            'Registration successful.',
            201
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:200',
            ],

            'password' => [
                'required',
                'string',
                'max:200',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $user = User::query()
            ->where('email', $data['email'])
            ->first();

        if (
            ! $user ||
            ! Hash::check($data['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been disabled.',
            ]);
        }

        return $this->issueToken(
            $user,
            $data['device_name'] ?? 'mobile-app',
            'Login successful.',
            200
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Access Token
    |--------------------------------------------------------------------------
    |
    | This endpoint does NOT require api.token middleware.
    |
    | Access token expires
    |      ↓
    | Refresh token
    |      ↓
    | New access token
    |      ↓
    | New refresh token
    |
    */

    public function refresh(Request $request)
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $hash = hash('sha256', $data['refresh_token']);

        return DB::transaction(function () use ($hash) {
            $refreshToken = RefreshToken::query()
                ->where('token_hash', $hash)
                ->lockForUpdate()
                ->first();

            if (! $refreshToken) {
                throw ValidationException::withMessages([
                    'refresh_token' => 'Invalid refresh token.',
                ]);
            }

            if ($refreshToken->revoked_at !== null) {
                throw ValidationException::withMessages([
                    'refresh_token' => 'Refresh token has been revoked.',
                ]);
            }

            if (! $refreshToken->expires_at || $refreshToken->expires_at->isPast()) {
                $refreshToken->delete();

                throw ValidationException::withMessages([
                    'refresh_token' => 'Refresh token has expired. Please login again.',
                ]);
            }

            $user = User::find($refreshToken->user_id);

            if (! $user || ! $user->isActive()) {
                $refreshToken->delete();

                throw ValidationException::withMessages([
                    'refresh_token' => 'Your account is unavailable. Please login again.',
                ]);
            }

            // Read the device name before deleting the old access token.
            $deviceName = $refreshToken->apiToken?->name ?? 'mobile-app';

            if ($refreshToken->api_token_id) {
                ApiToken::whereKey($refreshToken->api_token_id)->delete();
            }

            $accessToken = $this->createAccessToken($user, $deviceName);
            $plainRefreshToken = Str::random(120);

            // Reuse the same refresh_tokens row.
            $refreshToken->update([
                'api_token_id' => $accessToken['model']->id,
                'token_hash' => hash('sha256', $plainRefreshToken),
                'expires_at' => now()->addDays($this->refreshTokenTtlDays()),
                'revoked_at' => null,
                'replaced_by' => null,
            ]);

            return $this->success([
                'token' => $accessToken['plain'],
                'refresh_token' => $plainRefreshToken,
                'token_type' => 'Bearer',
                'expires_at' => $accessToken['model']->expires_at?->toISOString(),
                'refresh_expires_at' => $refreshToken->expires_at?->toISOString(),
                'user' => $this->userPayload($user),
            ], 'Token refreshed successfully.');
        });
    }

    /*
     |--------------------------------------------------------------------------
     | Forgot Password - Send OTP
     |--------------------------------------------------------------------------
     */

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'max:200',
            ],
        ]);

        $email = strtolower(trim($data['email']));

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        /*
         * Do not reveal whether the email exists.
         */
        if (! $user) {
            return $this->success(
                null,
                'If an account exists for that email address, a password reset OTP has been sent.'
            );
        }

        /*
         * Delete previous unused OTPs.
         */
        PasswordResetOtp::query()
            ->where('email', $email)
            ->whereNull('used_at')
            ->delete();

        /*
         * Generate 6 digit OTP.
         */
        $otp = (string) random_int(
            100000,
            999999
        );

        /*
         * Save OTP hash.
         */
        PasswordResetOtp::create([
            'user_id' => $user->id,

            'email' => $email,

            'otp_hash' => Hash::make($otp),

            'expires_at' => now()->addMinutes(
                self::OTP_EXPIRES_MINUTES
            ),

            'attempts' => 0,

            'used_at' => null,
        ]);

        /*
         * Send OTP email.
         */
        Mail::to($user->email)->send(
            new PasswordResetOtpMail(
                otp: $otp,
                expiresInMinutes: self::OTP_EXPIRES_MINUTES,
                userName: $user->name
            )
        );

        return $this->success(
            null,
            'If an account exists for that email address, a password reset OTP has been sent.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verify Password Reset OTP
    |--------------------------------------------------------------------------
    */

    public function verifyResetOtp(Request $request)
    {
        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'max:200',
            ],

            'otp' => [
                'required',
                'digits:6',
            ],
        ]);

        $email = strtolower(trim($data['email']));

        $record = PasswordResetOtp::query()
            ->where('email', $email)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (! $record) {
            throw ValidationException::withMessages([
                'otp' => 'This OTP is invalid or has expired.',
            ]);
        }

        if ($record->expires_at->isPast()) {
            $record->delete();

            throw ValidationException::withMessages([
                'otp' => 'This OTP has expired. Please request a new OTP.',
            ]);
        }

        if ($record->attempts >= self::MAX_OTP_ATTEMPTS) {
            throw ValidationException::withMessages([
                'otp' => 'Too many incorrect attempts. Please request a new OTP.',
            ]);
        }

        /*
         * Count every verification attempt.
         */
        $record->increment('attempts');

        if (! Hash::check(
            $data['otp'],
            $record->otp_hash
        )) {
            throw ValidationException::withMessages([
                'otp' => 'The OTP you entered is incorrect.',
            ]);
        }

        /*
         * Generate temporary reset token.
         *
         * Token itself is NOT stored in database.
         * Only SHA-256 hash is used as cache key.
         */
        $resetToken = Str::random(80);

        $cacheKey = $this->resetTokenCacheKey(
            $resetToken
        );

        Cache::put(
            $cacheKey,
            [
                'user_id' => $record->user_id,
                'email' => $email,
            ],
            now()->addMinutes(
                self::RESET_TOKEN_MINUTES
            )
        );

        /*
         * OTP can only be used once.
         */
        $record->update([
            'used_at' => now(),
        ]);

        return $this->success([
            'reset_token' => $resetToken,

            'expires_in' =>
                self::RESET_TOKEN_MINUTES * 60,
        ], 'OTP verified successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'max:200',
            ],

            'reset_token' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:200',
                'confirmed',
            ],
        ]);

        $email = strtolower(trim($data['email']));

        /*
         * Find temporary reset session.
         */
        $cacheKey = $this->resetTokenCacheKey(
            $data['reset_token']
        );

        $resetData = Cache::get($cacheKey);

        if (! $resetData) {
            throw ValidationException::withMessages([
                'reset_token' => 'Invalid or expired password reset token.',
            ]);
        }

        /*
         * Make sure token belongs to same email.
         */
        if (
            ! isset($resetData['email']) ||
            strtolower($resetData['email']) !== $email
        ) {
            throw ValidationException::withMessages([
                'reset_token' => 'Invalid password reset token.',
            ]);
        }

        $user = User::query()
            ->find($resetData['user_id']);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Unable to reset password.',
            ]);
        }

        /*
         * Make sure account is active.
         */
        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been disabled.',
            ]);
        }

        /*
         * Update password.
         *
         * User model already has:
         * 'password' => 'hashed'
         */
        $user->password = $data['password'];

        $user->remember_token = Str::random(60);

        $user->save();

        /*
         * Security:
         *
         * Invalidate all API access tokens.
         */
        ApiToken::query()
            ->where('user_id', $user->id)
            ->delete();

        /*
         * IMPORTANT:
         *
         * Also revoke all refresh tokens.
         *
         * Otherwise an old refresh token could create
         * a new access token after password reset.
         */
        RefreshToken::query()
            ->where('user_id', $user->id)
            ->delete();

        /*
         * Delete temporary reset token.
         * It cannot be reused.
         */
        Cache::forget($cacheKey);

        /*
         * Clean any remaining unused reset OTPs.
         */
        PasswordResetOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        return $this->success(
            null,
            'Password reset successfully. Please login again.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->success(
                null,
                'Your email address is already verified.'
            );
        }

        $user->sendEmailVerificationNotification();

        return $this->success(
            null,
            'A new verification email has been sent.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        return $this->success(
            $this->userPayload(
                $request->user()
            ),
            'Account details retrieved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $token = $request->attributes->get(
            'api_token'
        );

        if ($token instanceof ApiToken) {

            /*
             * Revoke refresh tokens connected
             * to this access token.
             */
            RefreshToken::query()
                ->where('api_token_id', $token->id)
                ->delete();

            $token->delete();
        }

        return $this->success(
            null,
            'Logged out successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Logout All
    |--------------------------------------------------------------------------
    */

    public function logoutAll(Request $request)
    {
        $userId = $request->user()->id;

        /*
         * Revoke all refresh tokens.
         */
        RefreshToken::query()
            ->where('user_id', $userId)
            ->delete();

        ApiToken::query()
            ->where('user_id', $userId)
            ->delete();

        return $this->success(
            null,
            'All API sessions have been logged out.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | API Token
    |--------------------------------------------------------------------------
    */

    private function issueToken(
        User $user,
        string $name,
        string $message,
        int $status
    ) {
        $accessToken = $this->createAccessToken($user, $name);
        $plainRefreshToken = Str::random(120);

        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'api_token_id' => $accessToken['model']->id,
            'token_hash' => hash('sha256', $plainRefreshToken),
            'expires_at' => now()->addDays($this->refreshTokenTtlDays()),
        ]);

        return $this->success([
            'token' => $accessToken['plain'],
            'refresh_token' => $plainRefreshToken,
            'token_type' => 'Bearer',
            'expires_at' => $accessToken['model']->expires_at?->toISOString(),
            'refresh_expires_at' => $refreshToken->expires_at?->toISOString(),
            'user' => $this->userPayload($user),
        ], $message, $status);
    }

    private function createAccessToken(User $user, string $name = 'mobile-app'): array
    {
        $plain = Str::random(80);

        $model = ApiToken::create([
            'user_id' => $user->id,
            'name' => $name,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addMinutes($this->accessTokenTtlMinutes()),
        ]);

        return [
            'plain' => $plain,
            'model' => $model,
        ];
    }

    private function accessTokenTtlMinutes(): int
    {
        return max(1, (int) env('API_ACCESS_TOKEN_TTL_MINUTES', 15));
    }

    private function refreshTokenTtlDays(): int
    {
        return max(1, (int) env('API_REFRESH_TOKEN_TTL_DAYS', 365));
    }

    /*
     |--------------------------------------------------------------------------
     | Reset Token Cache Key
     |--------------------------------------------------------------------------
     */

    private function resetTokenCacheKey(
        string $token
    ): string {
        return 'password_reset_api:' . hash(
            'sha256',
            $token
        );
    }

    /*
    |--------------------------------------------------------------------------
    | User Payload
    |--------------------------------------------------------------------------
    */

    public static function userPayload(
        User $user
    ): array {
        return [
            'id' => $user->id,

            'name' => $user->name,

            'username' => $user->username,

            'email' => $user->email,

            'role' => $user->role,

            'status' => $user->status,

            'email_verified' =>
                $user->hasVerifiedEmail(),
        ];
    }
}