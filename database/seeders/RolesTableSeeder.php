<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('roles')->delete();

        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'candidate', 'guard_name' => 'web'],
            ['name' => 'company', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}