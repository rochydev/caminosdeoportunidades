<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('accounts')->updateOrInsert(
            ['id' => 1],
            [
                'role' => 'CANDIDATE',
                'email' => 'candidate1@demo.com',
                'password_hash' => Hash::make('12345678'),
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('accounts')->updateOrInsert(
            ['id' => 2],
            [
                'role' => 'CANDIDATE',
                'email' => 'candidate2@demo.com',
                'password_hash' => Hash::make('12345678'),
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('accounts')->updateOrInsert(
            ['id' => 3],
            [
                'role' => 'COMPANY',
                'email' => 'company1@demo.com',
                'password_hash' => Hash::make('12345678'),
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}