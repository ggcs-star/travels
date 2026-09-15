<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\PointTransaction;
use App\Models\UserProfile;
use App\Services\Points\PointWalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly PointWalletService $pointWalletService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Dashboard
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $user = $request->user();

        $profile = UserProfile::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $wallet = $this->pointWalletService->walletFor($user);

        $recentTransactions = PointTransaction::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('profile.index', [
            'user' => $user,
            'profile' => $profile,
            'wallet' => $wallet,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Profile Page
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request): View
    {
        $user = $request->user();

        $profile = UserProfile::firstOrCreate([
            'user_id' => $user->id,
        ]);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Profile
    |--------------------------------------------------------------------------
    |
    | User can update:
    | - Name
    | - Phone
    | - Other profile information if those fields are present
    |
    | Username and email are intentionally NOT updated here.
    |
    */

public function update(
    UpdateProfileRequest $request
): RedirectResponse {
    $user = $request->user();

    $data = $request->validated();

    $user->update([
        'name' => $data['name'],
    ]);

    UserProfile::updateOrCreate(
        [
            'user_id' => $user->id,
        ],
        [
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address_line_1' => $data['address_line_1'] ?? null,
            'address_line_2' => $data['address_line_2'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
        ]
    );

    return redirect()
        ->route('profile')
        ->with('success', 'Profile updated successfully.');
}

    /*
    |--------------------------------------------------------------------------
    | Travel Points / Wallet
    |--------------------------------------------------------------------------
    */

    public function points(Request $request): View
    {
        $user = $request->user();

        $wallet = $this->pointWalletService->walletFor($user);

        $transactions = $this->pointWalletService->transactions(
            $user,
            20
        );

        return view('profile.points', [
            'user' => $user,
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}