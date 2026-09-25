<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display all users.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->where('role', 'user')
            ->withCount('bookings')
            ->with('pointWallet')
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = trim((string) $request->input('search'));

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->filled('role'),
                fn ($query) => $query->where('role', $request->input('role'))
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display a single user.
     */
    public function show(User $user): View
    {
        $user->load('pointWallet');

        $user->loadCount('bookings');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the edit form.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user's account details.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],

            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->id),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in(['user', 'admin']),
            ],

            'status' => ['required', 'boolean'],
        ]);

        if (
            (int) $user->id === (int) $request->user()->id
            && $data['role'] !== 'admin'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'You cannot remove your own admin role.',
                ]);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Soft delete a user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $user->id === (int) $request->user()->id) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        if (
            $user->role === 'admin'
            && User::where('role', 'admin')->count() <= 1
        ) {
            return back()->with(
                'error',
                'You cannot delete the last remaining admin account.'
            );
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
