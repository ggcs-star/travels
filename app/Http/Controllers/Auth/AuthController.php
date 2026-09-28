<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const OTP_EXPIRES_MINUTES = 10;
    private const MAX_OTP_ATTEMPTS = 5;
private const RESEND_SECONDS = 10;
    private const RESET_SESSION_SECONDS = 900;

    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:200'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been disabled.',
            ]);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:200', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4', 'max:200', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $this->generateUniqueUsername($validated['name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registration successful. Please verify your email address.');
    }

    protected function generateUniqueUsername(string $name): string
    {
        $base = Str::slug($name, '_');

        if ($base === '') {
            $base = 'user';
        }

        $base = Str::substr($base, 0, 90);
        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $suffix++;
            $username = $base . '_' . $suffix;
        }

        return $username;
    }

    /*
    |--------------------------------------------------------------------------
    | Forgot Password - Email OTP
    |--------------------------------------------------------------------------
    */

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

 public function sendPasswordResetOtp(Request $request)
{
    try {

        // 1. Validate email
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:200'],
        ]);

        $email = strtolower(trim($validated['email']));

        // 2. Find user
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return back()->with(
                'error',
                'No account found with this email address.'
            );
        }

        // 3. Delete old unused OTP
        PasswordResetOtp::where('email', $email)
            ->whereNull('used_at')
            ->delete();

        // 4. Generate OTP
        $otp = (string) random_int(100000, 999999);

        // 5. Save OTP WITHOUT mass assignment
        $resetOtp = new PasswordResetOtp();

        $resetOtp->user_id = $user->id;
        $resetOtp->email = $email;
        $resetOtp->otp_hash = Hash::make($otp);
        $resetOtp->expires_at = now()->addMinutes(10);
        $resetOtp->attempts = 0;
        $resetOtp->used_at = null;

        $resetOtp->save();

        // 6. TEMPORARY: use raw mail instead of PasswordResetOtpMail
        Mail::raw(
            "Your Travels password reset OTP is: {$otp}\n\n"
            . "This OTP is valid for 10 minutes.\n"
            . "Do not share this OTP with anyone.",
            function ($message) use ($user) {
                $message
                    ->to($user->email)
                    ->subject('Travels - Password Reset OTP');
            }
        );

        // 7. Save session
        session([
            'password_reset_email' => $email,
            'password_reset_otp_sent_at' => now()->timestamp,
        ]);

        // 8. Redirect to OTP page
        return redirect()
            ->route('password.otp.form')
            ->with(
                'success',
                'OTP has been sent successfully to your email.'
            );

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
}
    public function showPasswordOtp()
    {
        $email = session('password_reset_email');

        if (!$email) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Please enter your email address first.');
        }

        return view('auth.password-otp', compact('email'));
    }

    public function verifyPasswordResetOtp(Request $request)
    {
        $email = session('password_reset_email');

        if (!$email) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Your password reset session has expired. Please start again.');
        }

        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $record = PasswordResetOtp::where('email', $email)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (!$record || $record->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => 'This OTP is invalid or has expired. Please request a new OTP.',
            ]);
        }

        if ($record->attempts >= self::MAX_OTP_ATTEMPTS) {
            throw ValidationException::withMessages([
                'otp' => 'Too many incorrect attempts. Please request a new OTP.',
            ]);
        }

        $record->increment('attempts');
        $record->refresh();

        if (!Hash::check($validated['otp'], $record->otp_hash)) {
            throw ValidationException::withMessages([
                'otp' => 'The OTP you entered is incorrect.',
            ]);
        }

        // One-time use: mark this OTP as consumed.
        $record->update(['used_at' => now()]);

        session([
            'password_reset_verified' => true,
            'password_reset_user_id' => $record->user_id,
            'password_reset_verified_at' => now()->timestamp,
        ]);

        return redirect()
            ->route('password.reset.form')
            ->with('success', 'OTP verified. Please create your new password.');
    }

    public function resendPasswordResetOtp()
    {
        $email = session('password_reset_email');

        if (!$email) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Please start the password reset process again.');
        }

        $sentAt = (int) session('password_reset_otp_sent_at', 0);
        $remaining = self::RESEND_SECONDS - (now()->timestamp - $sentAt);

        if ($remaining > 0) {
            throw ValidationException::withMessages([
                'otp' => "Please wait {$remaining} seconds before requesting another OTP.",
            ]);
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user) {
            PasswordResetOtp::where('email', $email)
                ->whereNull('used_at')
                ->delete();

            $otp = (string) random_int(100000, 999999);

            PasswordResetOtp::create([
                'user_id' => $user->id,
                'email' => $email,
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(self::OTP_EXPIRES_MINUTES),
                'attempts' => 0,
                'used_at' => null,
            ]);

            Mail::to($user->email)->send(
                new PasswordResetOtpMail(
                    otp: $otp,
                    expiresInMinutes: self::OTP_EXPIRES_MINUTES,
                    userName: $user->name
                )
            );
        }

        session([
            'password_reset_otp_sent_at' => now()->timestamp,
        ]);

        return back()->with('success', 'If an account exists for that email, a new OTP has been sent.');
    }

   public function showResetPassword()
{
    if (!$this->passwordResetSessionIsValid()) {
        return redirect()
            ->route('password.request')
            ->with('error', 'Please verify the OTP first.');
    }

    return view('auth.reset-password');
}
    public function resetPassword(Request $request)
    {
        if (!$this->passwordResetSessionIsValid()) {
            $this->clearPasswordResetSession();

            return redirect()
                ->route('password.request')
                ->with('error', 'Your password reset session has expired. Please start again.');
        }

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'max:200',
                'confirmed',
            ],
        ]);

        $user = User::find(session('password_reset_user_id'));

        if (!$user) {
            $this->clearPasswordResetSession();

            throw ValidationException::withMessages([
                'password' => 'Unable to complete password reset. Please try again.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        if (method_exists($user, 'apiTokens')) {
            $user->apiTokens()->delete();
        }

        PasswordResetOtp::where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $this->clearPasswordResetSession();

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been reset successfully. You can now sign in.');
    }

    protected function passwordResetSessionIsValid(): bool
    {
        if (!session('password_reset_verified')) {
            return false;
        }

        $verifiedAt = (int) session('password_reset_verified_at', 0);

        return $verifiedAt > 0
            && (now()->timestamp - $verifiedAt) <= self::RESET_SESSION_SECONDS;
    }

    protected function clearPasswordResetSession(): void
    {
        session()->forget([
            'password_reset_email',
            'password_reset_otp_sent_at',
            'password_reset_verified',
            'password_reset_user_id',
            'password_reset_verified_at',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Register Page
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /*
    |--------------------------------------------------------------------------
    | Email Verification Notice
    |--------------------------------------------------------------------------
    */

    public function verificationNotice()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('auth.verify-email');
    }

    /*
    |--------------------------------------------------------------------------
    | Verify Email
    |--------------------------------------------------------------------------
    */

    public function verifyEmail(Request $request, $id, $hash)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid verification link.');
        }

        $user = User::findOrFail($id);

        if (!hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            abort(403, 'Invalid verification link.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()
            ->route('home')
            ->with('success', 'Your email has been verified successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Resend Verification
    |--------------------------------------------------------------------------
    */

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $user->sendEmailVerificationNotification();

        return back()->with(
            'success',
            'A new verification email has been sent.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Change Password
    |--------------------------------------------------------------------------
    */

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:4', 'max:200', 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->session()->regenerate();

        return back()->with(
            'success',
            'Your password has been changed successfully.'
        );
    }
}
