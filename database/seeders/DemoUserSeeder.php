<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Points\PointWalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'name' => 'Demo Admin',
                'username' => 'Demo Admin',
                'role' => 'admin',
                'starting_points' => 0,
            ],
            [
                'email' => 'customer@example.com',
                'name' => 'Demo Customer',
                'username' => 'Demo Customer',
                'role' => 'user',
                'starting_points' => 500,
            ],
            [
                'email' => 'demo@example.com',
                'name' => 'Demo User',
                'username' => 'Demo User',
                'role' => 'user',
                'starting_points' => 200,
            ],
        ];

        $wallets = app(PointWalletService::class);

        foreach ($users as $user) {
            $model = User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'password' => Hash::make('12345678'),
                    'role' => $user['role'],
                    'status' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Starting Wallet Balance
            |--------------------------------------------------------------------------
            |
            | Gives demo customers points to test booking discounts/redemption with.
            | Idempotent: the reference is unique per user so re-running the seeder
            | never credits points twice.
            */

            if ($user['starting_points'] > 0) {
                $wallets->creditOnce(
                    user: $model,
                    points: $user['starting_points'],
                    source: 'admin_adjustment',
                    reference: 'DEMO_SEED_POINTS:' . $model->id,
                    description: 'Starting demo points balance.',
                );
            }
        }
    }
}