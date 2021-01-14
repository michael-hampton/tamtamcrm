<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();


        return [
            'user_id'          => $user->id,
            'account_id'       => 1,
            'private_notes'    => null,
            'public_notes'     => null,
            'is_deleted'       => false,
            'assigned_to'      => null,
            'industry_id'      => 1,
            'currency_id'      => 1,
            'country_id'       => 225,
            'custom_value1'    => $this->faker->text(20),
            'custom_value2'    => $this->faker->text(20),
            'custom_value3'    => $this->faker->text(20),
            'custom_value4'    => $this->faker->text(20),
            'name'             => $this->faker->company,
            'website'          => $this->faker->url,
            'phone_number'     => $this->faker->phoneNumber,
            'email'            => $this->faker->email,
            'address_1'        => $this->faker->streetName,
            'address_2'        => $this->faker->streetAddress,
            'town'             => $this->faker->word,
            'city'             => $this->faker->city,
            'postcode'         => $this->faker->postcode,
            'deleted_at'       => null,
            'vat_number'       => null,
            'balance'          => null,
            'paid_to_date'     => null,
            'number'           => null
        ];
    }
}
