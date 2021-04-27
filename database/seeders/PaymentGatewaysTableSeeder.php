<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('payment_gateways')->delete();

        DB::table('payment_gateways')->insert(array(
            0 =>
                array(
                    'id'                      => 1,
                    'name'                    => 'Authorize',
                    'key'                     => '8ab2dce2',
                    'provider'                => 'Authorize',
                    'default_gateway_type_id' => 1,
                    'created_at'              => '2021-01-18 23:30:05',
                    'updated_at'              => '2021-01-18 23:30:05',
                    'offsite_only'            => 0,
                    'is_custom'               => 0,
                ),
            1 =>
                array(
                    'id'                      => 2,
                    'name'                    => 'Stripe',
                    'key'                     => '13bb8d58',
                    'provider'                => 'Stripe',
                    'default_gateway_type_id' => 1,
                    'created_at'              => '2021-01-18 23:30:05',
                    'updated_at'              => '2021-01-18 23:30:05',
                    'offsite_only'            => 0,
                    'is_custom'               => 0,
                ),
            2 =>
                array(
                    'id'                      => 3,
                    'name'                    => 'Custom',
                    'key'                     => '4ntgik8629',
                    'provider'                => 'Custom',
                    'default_gateway_type_id' => 1,
                    'created_at'              => '2021-01-18 23:30:05',
                    'updated_at'              => '2021-01-18 23:30:05',
                    'offsite_only'            => 0,
                    'is_custom'               => 0,
                ),
            3 =>
                array(
                    'id'                      => 4,
                    'name'                    => 'PayPal',
                    'key'                     => '64bcbdce',
                    'provider'                => 'PayPal_Express',
                    'default_gateway_type_id' => 1,
                    'created_at'              => null,
                    'updated_at'              => null,
                    'offsite_only'            => 0,
                    'is_custom'               => 0,
                ),
            4 =>
                array(
                    'id'                      => 5,
                    'name'                    => 'Apple Pay',
                    'key'                     => 'wmqxwzcdst',
                    'provider'                => 'Stripe',
                    'default_gateway_type_id' => 1,
                    'created_at'              => null,
                    'updated_at'              => null,
                    'offsite_only'            => 0,
                    'is_custom'               => 0,
                ),
            5 =>
                array(
                    'id'                      => 6,
                    'name'                    => 'Stripe Connect',
                    'key'                     => 'ocglwiyeow',
                    'provider'                => 'Stripe',
                    'default_gateway_type_id' => 1,
                    'created_at'              => null,
                    'updated_at'              => null,
                    'offsite_only'            => 0,
                    'is_custom'               => 0,
                ),
            6 =>
                array(
                    'id'                      => 7,
                    'name'                    => 'Braintree',
                    'key'                     => 'dlmqa4gvpy',
                    'provider'                => 'Braintree',
                    'default_gateway_type_id' => 1,
                    'created_at'              => null,
                    'updated_at'              => null,
                    'offsite_only'            => 0,
                    'is_custom'               => 0,
                ),
        ));


    }
}