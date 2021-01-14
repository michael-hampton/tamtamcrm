<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();

        return [
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'message' => $this->faker->sentence,
            'has_seen' => 1,
            'direction' => 1
        ];
    }
}
