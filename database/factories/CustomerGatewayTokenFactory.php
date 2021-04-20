<?php

namespace Database\Factories;

use App\Models\CompanyGateway;
use App\Models\Customer;
use App\Models\CustomerGatewayToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGatewayTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerGatewayToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::factory()->create();
        $company_gateway = CompanyGateway::first();
        $account = \App\Models\Account::first();
        $user = User::factory()->create();

        return [
            'company_gateway_id' => $company_gateway->id,
            'customer_id'        => $customer->id,
            'account_id'         => $account->id,
            'user_id'            => $user->id,
            'token'              => $this->faker->password(10)
        ];
    }
}
