<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

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
            'account_id'   => 1,
            'user_id'      => $user->id,
            'name'         => $this->faker->text,
            'description'  => $this->faker->text,
            'is_completed' => 0,
            'customer_id'  => $customer->id,
            'due_date'     => $this->faker->dateTime(),
            'source_type'  => 1,
            'task_status_id'  => 1,
            'task_rate'    => null,
            'valued_at'    => $this->faker->randomNumber(3)
        ];
    }
}
