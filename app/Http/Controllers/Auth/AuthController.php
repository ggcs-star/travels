<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Page
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    |
    | Same login for User + Admin.
    |
    | admin -> Admin Dashboard
    | user  -> Website
    |
    */

    public function login(Request $request)
    {
        $credentials = $request->validate([
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
        ]);

        $remember = $request->boolean('remember');


        /*
        |--------------------------------------------------------------------------
        | Find User
        |--------------------------------------------------------------------------
        */

        $user = User::where(
            'email',
            $credentials['email']
        )->first();


        /*
        |--------------------------------------------------------------------------
        | Invalid Credentials
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Account Status
        |--------------------------------------------------------------------------
        */

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been disabled.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $credentials['password'],
            $user->password
        )) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user,
            $remember
        );


        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Role Based Redirect
        |--------------------------------------------------------------------------
        */

        if ($user->isAdmin()) {
            return redirect()->intended(
                route('admin.dashboard')
            );
        }

        return redirect()->intended(
            route('dashboard')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    |
    | Registration happens from the same login.blade.php.
    |
    | Every newly registered account is always a normal user.
    |
    */

    public function register(Request $request)
    {
        $validated = $request->validate([
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
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $validated['name'],

            'username' => $validated['username'],

            'email' => $validated['email'],

            'password' => Hash::make(
                $validated['password']
            ),

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT
            |--------------------------------------------------------------------------
            | Registration can NEVER create an admin.
            */

            'role' => 'user',

            'status' => true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Email Verification Event
        |--------------------------------------------------------------------------
        */

        event(
            new Registered($user)
        );


        /*
        |--------------------------------------------------------------------------
        | Login After Registration
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();


        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Registration successful. Please verify your email address.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Send Password Reset Link
    |--------------------------------------------------------------------------
    */

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:200'],
        ]);

        $status = Password::sendResetLink([
            'email' => strtolower(trim($validated['email'])),
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => 'We could not send a password reset link to that email address.',
            ]);
        }

        return back()->with(
            'success',
            'If an account exists for that email address, a password reset link has been sent.'
        );
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:200'],
            'password' => ['required', 'string', 'min:4', 'max:200', 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'token' => $validated['token'],
                'email' => strtolower(trim($validated['email'])),
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->apiTokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => 'This password reset link is invalid or has expired.',
            ]);
        }

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Your password has been reset successfully. You can now sign in.'
            );
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

    public function verifyEmail(
        Request $request,
        $id,
        $hash
    ) {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid verification link.');
        }

        $user = User::findOrFail($id);

        if (
            !hash_equals(
                sha1($user->getEmailForVerification()),
                $hash
            )
        ) {
            abort(403, 'Invalid verification link.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()
            ->route('home')
            ->with(
                'success',
                'Your email has been verified successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Resend Verification
    |--------------------------------------------------------------------------
    */

    public function resendVerification(
        Request $request
    ) {
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


    /*
    |--------------------------------------------------------------------------
    | Update Password
    |--------------------------------------------------------------------------
    */

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'string',
                'min:4',
                'max:200',
                'confirmed',
            ],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        return back()->with(
            'success',
            'Your password has been changed successfully.'
        );
    }
}


