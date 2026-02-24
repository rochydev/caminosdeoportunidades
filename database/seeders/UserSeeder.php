<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('Password123!'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@demo.com'],
            [
                'name' => 'User Demo',
                'password' => Hash::make('Password123!'),
            ]
        );
    }
}