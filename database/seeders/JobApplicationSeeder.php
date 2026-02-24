<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('job_applications')->updateOrInsert(
            ['id' => 1],
            [
                'offer_id' => 1,
                'candidate_account_id' => 1,
                'status' => 'SENT',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('job_applications')->updateOrInsert(
            ['id' => 2],
            [
                'offer_id' => 2,
                'candidate_account_id' => 2,
                'status' => 'IN_REVIEW',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}