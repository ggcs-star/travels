<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\UserProfile;
use App\Services\Points\PointWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    public function profile(
        Request $request,
        PointWalletService $pointWalletService
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | User Profile
        |--------------------------------------------------------------------------
        |
        | Create profile automatically if it does not exist.
        |
        */

        $profile = UserProfile::firstOrCreate([
            'user_id' => $user->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Points Wallet
        |--------------------------------------------------------------------------
        */

        $wallet = $pointWalletService->walletFor($user);

        /*
        |--------------------------------------------------------------------------
        | Points Transactions
        |--------------------------------------------------------------------------
        |
        | Latest 50 transactions.
        |
        */

        $transactions = $pointWalletService->transactions(
            $user,
            50
        );

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return $this->success([
            'user' => AuthController::userPayload(
                $user->fresh()
            ),

            'profile' => $profile,

            'points' => [
                'wallet' => $wallet,
                'transactions' => $transactions,
            ],
        ], 'Profile retrieved successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Update Profile
    |--------------------------------------------------------------------------
    */

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:40',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            'gender' => [
                'nullable',
                Rule::in([
                    'male',
                    'female',
                    'other',
                    'prefer_not_to_say',
                ]),
            ],

            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        */

        $user->update([
            'name' => trim($data['name']),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update / Create Profile
        |--------------------------------------------------------------------------
        */

        $profile = UserProfile::updateOrCreate(
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

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return $this->success([
            'user' => AuthController::userPayload(
                $user->fresh()
            ),

            'profile' => $profile->fresh(),
        ], 'Profile updated successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Change Password
    |--------------------------------------------------------------------------
    */

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => [
                'required',
                'string',
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

        /*
        |--------------------------------------------------------------------------
        | Verify Current Password
        |--------------------------------------------------------------------------
        */

        if (! Hash::check(
            $data['current_password'],
            $user->password
        )) {
            return $this->error(
                'The current password is incorrect.',
                422,
                [
                    'current_password' => [
                        'The current password is incorrect.',
                    ],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        */

        $user->update([
            'password' => Hash::make(
                $data['password']
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return $this->success(
            null,
            'Password changed successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Points
    |--------------------------------------------------------------------------
    */

    public function points(
        Request $request,
        PointWalletService $pointWalletService
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Wallet
        |--------------------------------------------------------------------------
        */

        $wallet = $pointWalletService->walletFor(
            $user
        );

        /*
        |--------------------------------------------------------------------------
        | Transactions
        |--------------------------------------------------------------------------
        */

        $transactions = $pointWalletService->transactions(
            $user,
            50
        );

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return $this->success([
            'wallet' => $wallet,

            'transactions' => $transactions,
        ], 'Travel points retrieved successfully.');
    }
}