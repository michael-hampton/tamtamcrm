<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

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
            'created_by' => $user->id,
            'account_id' => 1,
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'location' => $this->faker->word,
            'beginDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'endDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'customer_id' => $customer->id,
            'event_type' => 2
        ];
    }
}
