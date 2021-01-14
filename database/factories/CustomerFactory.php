<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();

        $settings = (new \App\Settings\CustomerSettings())->getCustomerDefaults();

        return [
            'name'          => $this->faker->name(),
            'number'        => $this->faker->ean8(),
            'website'       => $this->faker->url,
            'currency_id'   => 2,
            'private_notes' => $this->faker->text(200),
            'balance'       => 2020.22,
            'paid_to_date'  => 0,
            'custom_value1' => $this->faker->text(20),
            'custom_value2' => $this->faker->text(20),
            'custom_value3' => $this->faker->text(20),
            'custom_value4' => $this->faker->text(20),
            'settings'      => $settings,
            'account_id'    => 1,
            'user_id'       => $user->id,
            'phone'         => $this->faker->phoneNumber,
            'status'        => 1

        ];
    }
}
