<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('users')->delete();

        $usersData = [
            [
                'id' => 1,
                'name' => 'Admin',
                'surname1' => 'Sistema',
                'surname2' => null,
                'alias' => 'admin',
                'email' => 'admin@demo.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'admin',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Pol',
                'surname1' => 'García',
                'surname2' => 'Martínez',
                'alias' => 'pol',
                'email' => 'pol@demo.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'candidate',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Roger',
                'surname1' => 'Malgrat',
                'surname2' => 'González',
                'alias' => 'roger',
                'email' => 'roger@demo.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'candidate',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Arnau',
                'surname1' => 'Vidal',
                'surname2' => 'López',
                'alias' => 'arnau',
                'email' => 'arnau@demo.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'candidate',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Pau',
                'surname1' => 'Ferrer',
                'surname2' => 'Soler',
                'alias' => 'pau',
                'email' => 'pau@demo.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'candidate',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Mercadona',
                'surname1' => 'S.A.',
                'surname2' => null,
                'alias' => 'mercadona',
                'email' => 'rrhh@mercadona.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'company',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Inditex',
                'surname1' => 'S.A.',
                'surname2' => null,
                'alias' => 'inditex',
                'email' => 'rrhh@inditex.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'company',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Telefónica',
                'surname1' => 'S.A.',
                'surname2' => null,
                'alias' => 'telefonica',
                'email' => 'rrhh@telefonica.com',
                'email_verified_at' => null,
                'password' => bcrypt('12345678'),
                'status' => 'ACTIVE',
                'role_name' => 'company',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($usersData as $userData) {
            $roleName = $userData['role_name'];
            unset($userData['role_name']);

            $user = User::create($userData);
            $user->assignRole($roleName);
        }
    }
}
