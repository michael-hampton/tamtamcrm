<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentGatewayTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_gateway_types')->delete();
        
        \DB::table('payment_gateway_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'alias' => 'credit_card',
                'name' => 'Credit Card',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'alias' => 'paypal',
                'name' => 'PayPal',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'alias' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'alias' => 'sofort',
                'name' => 'Sofort',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'alias' => 'Alipay',
                'name' => 'Alipay',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}