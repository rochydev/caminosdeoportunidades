<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CandidateProfileSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('candidate_profiles')->updateOrInsert(
            ['account_id' => 1],
            [
                'account_id' => 1,
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'phone' => '600111222',
                'city' => 'Barcelona',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('candidate_profiles')->updateOrInsert(
            ['account_id' => 2],
            [
                'account_id' => 2,
                'first_name' => 'Ana',
                'last_name' => 'García',
                'phone' => '600333444',
                'city' => 'Madrid',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}