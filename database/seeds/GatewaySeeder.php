<?php

use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gateway_types = [
            0 => [
                'id'    => 1,
                'alias' => 'credit_card',
                'name'  => 'Credit Card'
            ],
            1 => [
                'id'    => 2,
                'alias' => 'paypal',
                'name'  => 'PayPal'
            ]
        ];

        foreach ($gateway_types as $gateway_type) {
            \Illuminate\Support\Facades\DB::table('payment_gateway_types')->insert(
                $gateway_type
            );
        }


        $arrGateways = [
            0 => [
                'name'                    => 'Authorize',
                'provider'                => 'Authorize',
                'key'                     => '8ab2dce2',
                'default_gateway_type_id' => 1
            ],
            1 => [
                'name'                    => 'Stripe',
                'provider'                => 'Stripe',
                'key'                     => '13bb8d58',
                'default_gateway_type_id' => 1
            ],
            2 => [
                'name'                    => 'PayPal',
                'provider'                => 'PayPal_Express',
                'key'                     => '64bcbdce',
                'default_gateway_type_id' => 2
            ],
            2 => [
                'name'                    => 'Custom',
                'provider'                => 'Custom',
                'key'                     => '4ntgik8629',
                'default_gateway_type_id' => 1
            ],
        ];

        foreach ($arrGateways as $arr_gateway) {
            \App\Models\PaymentGateway::create($arr_gateway);
        }
    }

}
