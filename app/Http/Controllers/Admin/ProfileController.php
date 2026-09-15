<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display admin profile.
     */
    public function index(Request $request): View
    {
        $admin = $request->user();

        abort_unless($admin && $admin->isAdmin(), 403);

return view('admin.profile.index', compact('admin'));
    }

    /**
     * Update admin profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($admin && $admin->isAdmin(), 403);

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z0-9_.-]+$/',
                'unique:users,username,' . $admin->id,
            ],
            'email' => [
                'required',
                'email',
                'max:200',
                'unique:users,email,' . $admin->id,
            ],
        ]);

        $admin->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        return back()->with(
            'success',
            'Profile updated successfully.'
        );
    }

    /**
     * Display change password page.
     */
    public function password(Request $request): View
    {
        $admin = $request->user();

        abort_unless($admin && $admin->isAdmin(), 403);

return view('admin.profile.change-password');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($admin && $admin->isAdmin(), 403);

        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $admin->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        $request->session()->regenerate();

        return back()->with(
            'success',
            'Password changed successfully.'
        );
    }
}