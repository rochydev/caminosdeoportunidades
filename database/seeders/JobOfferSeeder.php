<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class JobOfferSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('job_offers')->updateOrInsert(
            ['id' => 1],
            [
                'company_user_id' => 3,
                'title' => 'Desarrollador PHP',
                'description' => 'Oferta para desarrollador PHP con conocimientos de Laravel.',
                'status' => 'PUBLISHED',
                'city' => 'Barcelona',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('job_offers')->updateOrInsert(
            ['id' => 2],
            [
                'company_user_id' => 3,
                'title' => 'Frontend React',
                'description' => 'Oferta para desarrollador Frontend con React y TypeScript.',
                'status' => 'PUBLISHED',
                'city' => 'Madrid',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}