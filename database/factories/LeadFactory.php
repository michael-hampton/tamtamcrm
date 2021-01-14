<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        return [
            'address_1'   => $this->faker->streetAddress,
            'address_2'   => null,
            'zip'         => $this->faker->postcode,
            'city'        => $this->faker->city,
            'source_type' => 1,
            'account_id'  => 1,
            'user_id'     => $user->id,
            'task_status_id' => 1,
            'name'        => $this->faker->word,
            'description' => $this->faker->sentence,
            'first_name'  => $this->faker->firstName,
            'last_name'   => $this->faker->lastName,
            'phone'       => $this->faker->phoneNumber,
            'email'       => $this->faker->safeEmail
        ];
    }
}
