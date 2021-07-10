<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanFeaturesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('plan_features')->delete();
        
        DB::table('plan_features')->insert(array (
            0 => 
            array (
                'id' => 1,
                'plan_id' => 8,
                'feature_id' => 2,
                'value' => '100',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'CUSTOMER',
            ),
            1 => 
            array (
                'id' => 2,
                'plan_id' => 7,
                'feature_id' => 2,
                'value' => '100',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'CUSTOMER',
            ),
            2 => 
            array (
                'id' => 3,
                'plan_id' => 9,
                'feature_id' => 2,
                'value' => '9999',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'CUSTOMER',
            ),
            3 => 
            array (
                'id' => 4,
                'plan_id' => 10,
                'feature_id' => 2,
                'value' => '9999',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'CUSTOMER',
            ),
            4 => 
            array (
                'id' => 5,
                'plan_id' => 8,
                'feature_id' => 4,
                'value' => '100',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'DOCUMENT',
            ),
            5 => 
            array (
                'id' => 6,
                'plan_id' => 7,
                'feature_id' => 4,
                'value' => '100',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'DOCUMENT',
            ),
            6 => 
            array (
                'id' => 7,
                'plan_id' => 9,
                'feature_id' => 4,
                'value' => '9999',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'DOCUMENT',
            ),
            7 => 
            array (
                'id' => 8,
                'plan_id' => 10,
                'feature_id' => 4,
                'value' => '9999',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'DOCUMENT',
            ),
            8 => 
            array (
                'id' => 9,
                'plan_id' => 9,
                'feature_id' => 5,
                'value' => '1000',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'EMAIL',
            ),
            9 => 
            array (
                'id' => 10,
                'plan_id' => 10,
                'feature_id' => 5,
                'value' => '1000',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'EMAIL',
            ),
            10 => 
            array (
                'id' => 11,
                'plan_id' => 8,
                'feature_id' => 5,
                'value' => '500',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'EMAIL',
            ),
            11 => 
            array (
                'id' => 12,
                'plan_id' => 7,
                'feature_id' => 5,
                'value' => '500',
                'note' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
                'slug' => 'EMAIL',
            ),
        ));
        
        
    }
}