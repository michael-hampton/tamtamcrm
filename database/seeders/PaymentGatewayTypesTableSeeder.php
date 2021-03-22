<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewayTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('payment_gateway_types')->delete();
        
        DB::table('payment_gateway_types')->insert(array (
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
        ));
        
        
    }
}