<?php

namespace Database\Seeders;

use App\Models\User;
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
            ],
            [
                'email' => 'customer@example.com',
                'name' => 'Demo Customer',
                'username' => 'Demo Customer',
                'role' => 'user',
            ],
            [
                'email' => 'demo@example.com',
                'name' => 'Demo User',
                'username' => 'Demo User',
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'password' => Hash::make('12345678'),
                    'role' => $user['role'],
                    'status' => true,
                ]
            );
        }
    }
}