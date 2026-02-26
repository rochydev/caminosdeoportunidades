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
            ['user_id' => 1],
            [
                'user_id' => 1,
                'first_name' => 'Lucia',
                'last_name' => 'Martinez Ortega',
                'phone' => '600111222',
                'city' => 'Barcelona',
                'photo_url' => null,
                'disability_type_id' => null,
                'disability_degree' => null,
                'accessibility_needs' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('candidate_profiles')->updateOrInsert(
            ['user_id' => 2],
            [
                'user_id' => 2,
                'first_name' => 'Carlos',
                'last_name' => 'Ruiz Santos',
                'phone' => '600333444',
                'city' => 'Madrid',
                'photo_url' => null,
                'disability_type_id' => null,
                'disability_degree' => null,
                'accessibility_needs' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
