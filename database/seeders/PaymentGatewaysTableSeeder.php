<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentGatewaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_gateways')->delete();
        
        \DB::table('payment_gateways')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Authorize',
                'key' => '8ab2dce2',
                'provider' => 'Authorize',
                'default_gateway_type_id' => 1,
                'created_at' => '2021-01-18 23:30:05',
                'updated_at' => '2021-01-18 23:30:05',
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Stripe',
                'key' => '13bb8d58',
                'provider' => 'Stripe',
                'default_gateway_type_id' => 1,
                'created_at' => '2021-01-18 23:30:05',
                'updated_at' => '2021-01-18 23:30:05',
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Custom',
                'key' => '4ntgik8629',
                'provider' => 'Custom',
                'default_gateway_type_id' => 1,
                'created_at' => '2021-01-18 23:30:05',
                'updated_at' => '2021-01-18 23:30:05',
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'PayPal',
                'key' => '64bcbdce',
                'provider' => 'PayPal_Express',
                'default_gateway_type_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Apple Pay',
                'key' => 'wmqxwzcdst',
                'provider' => 'Stripe',
                'default_gateway_type_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Stripe Connect',
                'key' => 'ocglwiyeow',
                'provider' => 'Stripe',
                'default_gateway_type_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Braintree',
                'key' => 'dlmqa4gvpy',
                'provider' => 'Braintree',
                'default_gateway_type_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'ACH',
                'key' => 'abcdkfgj',
                'provider' => 'Stripe',
                'default_gateway_type_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Sofort',
                'key' => 'fgfggf',
                'provider' => 'Stripe',
                'default_gateway_type_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Alipay',
                'key' => 'cv4lsn09d4',
                'provider' => 'Stripe',
                'default_gateway_type_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
                'offsite_only' => 0,
                'is_custom' => 0,
            ),
        ));
        
        
    }
}