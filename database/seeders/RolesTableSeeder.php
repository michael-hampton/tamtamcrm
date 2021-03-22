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
                'id' => 3,
                'name' => 'Admin',
                'display_name' => 'Admin',
                'description' => 'Admin',
                'created_at' => '2021-01-18 23:30:05',
                'updated_at' => '2021-01-18 23:30:05',
                'account_id' => 1,
                'user_id' => 5,
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'Manager',
                'display_name' => 'Manager',
                'description' => 'Manager',
                'created_at' => '2021-01-18 23:30:05',
                'updated_at' => '2021-01-18 23:30:05',
                'account_id' => 1,
                'user_id' => 5,
            ),
        ));
        
        
    }
}