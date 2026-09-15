<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => [
                'required', 'string', 'min:4', 'max:100',
                'regex:/^[A-Za-z0-9_.-]+$/',
                'unique:users,username',
            ],
            'email' => [
                'required', 'string', 'email', 'max:200',
                'unique:users,email',
            ],
            'password' => [
                'required', 'string', 'min:4', 'max:200', 'confirmed',
            ],
            'device_name' => ['nullable', 'string', 'max:100'],
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

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:200'],
            'password' => ['required', 'string', 'max:200'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::query()
            ->where('email', $data['email'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
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


    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:200'],
        ]);

        $status = Password::sendResetLink([
            'email' => strtolower(trim($data['email'])),
        ]);

        /*
         * Always return the same message to avoid revealing whether an
         * email address exists in the database.
         */
        if ($status !== Password::RESET_LINK_SENT) {
            return $this->success(
                null,
                'If an account exists for that email address, a password reset link has been sent.'
            );
        }

        return $this->success(
            null,
            'If an account exists for that email address, a password reset link has been sent.'
        );
    }


    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->success(null, 'Your email address is already verified.');
        }

        $user->sendEmailVerificationNotification();

        return $this->success(
            null,
            'A new verification email has been sent.'
        );
    }

    public function me(Request $request)
    {
        return $this->success(
            $this->userPayload($request->user()),
            'Account details retrieved successfully.'
        );
    }

    public function logout(Request $request)
    {
        $token = $request->attributes->get('api_token');

        if ($token instanceof ApiToken) {
            $token->delete();
        }

        return $this->success(null, 'Logged out successfully.');
    }

    public function logoutAll(Request $request)
    {
        ApiToken::query()
            ->where('user_id', $request->user()->id)
            ->delete();

        return $this->success(null, 'All API sessions have been logged out.');
    }

    private function issueToken(User $user, string $name, string $message, int $status)
    {
        $plain = Str::random(80);

        $token = ApiToken::create([
            'user_id' => $user->id,
            'name' => $name,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addDays(
                max(1, (int) env('API_TOKEN_TTL_DAYS', 30))
            ),
        ]);

        return $this->success([
            'token' => $plain,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at?->toISOString(),
            'user' => $this->userPayload($user),
        ], $message, $status);
    }

    public static function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'email_verified' => $user->hasVerifiedEmail(),
        ];
    }
}
