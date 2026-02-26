<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Lucia',
                'surname1' => 'Martinez',
                'surname2' => 'Ortega',
                'alias' => 'lucia.candidata',
                'email' => 'lucia.martinez@caminos.local',
                'email_verified_at' => NULL,
                'password' => bcrypt('CambioSeguro123!'),
                'role' => 'CANDIDATE',
                'status' => 'ACTIVE',
                'remember_token' => NULL,
                'created_at' => '2026-02-25 10:00:00',
                'updated_at' => '2026-02-25 10:00:00',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Carlos',
                'surname1' => 'Ruiz',
                'surname2' => 'Santos',
                'alias' => 'carlos.candidato',
                'email' => 'carlos.ruiz@caminos.local',
                'email_verified_at' => NULL,
                'password' => bcrypt('CambioSeguro123!'),
                'role' => 'CANDIDATE',
                'status' => 'ACTIVE',
                'remember_token' => NULL,
                'created_at' => '2026-02-25 10:00:00',
                'updated_at' => '2026-02-25 10:00:00',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'InclusionTech',
                'surname1' => 'Recursos',
                'surname2' => 'Humanos',
                'alias' => 'inclusiontech.empresa',
                'email' => 'talento@inclusiontech.local',
                'email_verified_at' => NULL,
                'password' => bcrypt('CambioSeguro123!'),
                'role' => 'COMPANY',
                'status' => 'ACTIVE',
                'remember_token' => NULL,
                'created_at' => '2026-02-25 10:00:00',
                'updated_at' => '2026-02-25 10:00:00',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Admin',
                'surname1' => 'Plataforma',
                'surname2' => NULL,
                'alias' => 'admin.caminos',
                'email' => 'admin@caminos.local',
                'email_verified_at' => NULL,
                'password' => bcrypt('CambioSeguro123!'),
                'role' => 'ADMIN',
                'status' => 'ACTIVE',
                'remember_token' => NULL,
                'created_at' => '2026-02-25 10:00:00',
                'updated_at' => '2026-02-25 10:00:00',
            ),
        ));


    }
}
