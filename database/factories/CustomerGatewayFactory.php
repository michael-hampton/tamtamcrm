<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerGateway;
use http\Client\Curl\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGatewayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerGateway::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $account = \App\Models\Account::first();
        return [
            'account_id'    => $account->id,
            'customer_id'   => $customer->id,
        ];
    }
}
