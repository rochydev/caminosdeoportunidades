<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);

        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);

        $this->call(MediaTableSeeder::class);

        $this->call(CatalogSeeder::class);
        $this->call(DataSeeder::class);
    }
}
