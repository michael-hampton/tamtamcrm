<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('features')->delete();
        
        DB::table('features')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Number of Licences',
                'code' => 'LICENCE',
                'description' => 'Number of Licences',
                'interval_unit' => 'month',
                'interval_count' => 1,
                'sort_order' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Number of Customers',
                'code' => 'CUSTOMER',
                'description' => 'Number of Customers that can be created for an account',
                'interval_unit' => '',
                'interval_count' => 1,
                'sort_order' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Number of Users',
                'code' => 'USERS',
                'description' => 'Numbers of users that can be created on an account',
                'interval_unit' => 'month',
                'interval_count' => 1,
                'sort_order' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Number of documents',
                'code' => 'DOCUMENTS',
                'description' => 'Number of Documents that can be uploaded on an account',
                'interval_unit' => 'month',
                'interval_count' => 1,
                'sort_order' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}