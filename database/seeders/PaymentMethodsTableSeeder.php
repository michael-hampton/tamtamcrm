<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentMethodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_methods')->delete();
        
        \DB::table('payment_methods')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Apply Credit',
                'gateway_type_id' => NULL,
                'created_at' => '2021-01-18 23:30:04',
                'updated_at' => '2021-01-18 23:30:04',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Bank Transfer',
                'gateway_type_id' => 2,
                'created_at' => '2021-01-18 23:30:04',
                'updated_at' => '2021-01-18 23:30:04',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Cash',
                'gateway_type_id' => NULL,
                'created_at' => '2021-01-18 23:30:04',
                'updated_at' => '2021-01-18 23:30:04',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Debit',
                'gateway_type_id' => 1,
                'created_at' => '2021-01-18 23:30:04',
                'updated_at' => '2021-01-18 23:30:04',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'PayPal',
                'gateway_type_id' => 3,
                'created_at' => '2021-01-18 23:30:04',
                'updated_at' => '2021-01-18 23:30:04',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Check',
                'gateway_type_id' => NULL,
                'created_at' => '2021-01-18 23:30:04',
                'updated_at' => '2021-01-18 23:30:04',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'ACH',
                'gateway_type_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}