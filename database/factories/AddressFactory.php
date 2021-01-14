<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::factory()->create();

        return [
            'alias' => $this->faker->word,
            'address_1' => $this->faker->streetAddress,
            'address_2' => null,
            'zip' => $this->faker->postcode,
            'city' => $this->faker->city,
            'province_id' => 1,
            'country_id' => 225,
            'customer_id' => $customer->id,
            'status' => 1
        ];
    }
}
