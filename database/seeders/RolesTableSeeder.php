<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();

        DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'guard_name' => 'web',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'candidate',
                'guard_name' => 'web',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'company',
                'guard_name' => 'web',
            ),
        ));

    }
}