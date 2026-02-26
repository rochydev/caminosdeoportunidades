<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('company_profiles')->updateOrInsert(
            ['user_id' => 3],
            [
                'user_id' => 3,
                'company_name' => 'InclusionTech',
                'description' => 'Empresa enfocada en talento diverso y empleo inclusivo.',
                'sector' => 'Tecnologia',
                'city' => 'Barcelona',
                'contact_phone' => '934000111',
                'website' => 'https://inclusiontech.local',
                'logo_url' => null,
                'offers_adapted_positions' => true,
                'offers_remote_work' => true,
                'offers_reasonable_adjustments' => true,
                'validation_status' => 'VALIDATED',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
