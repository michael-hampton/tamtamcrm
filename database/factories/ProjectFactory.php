<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        return [
            'account_id'     => 1,
            'assigned_to'    => null,
            'user_id'        => $user->id,
            'customer_id'    => $customer->id,
            'name'           => $this->faker->text,
            'description'    => $this->faker->text,
            'is_completed'   => 0,
            'private_notes'  => null,
            'public_notes'   => null,
            'number'         => null,
            'budgeted_hours' => null,
            'task_rate'      => null,
            'due_date'       => null,
            'start_date'     => null,
            'deleted_at'     => null,
            'hide'           => 0,
            'custom_value1'  => $this->faker->numberBetween(1, 4),
            'custom_value2'  => $this->faker->numberBetween(1, 4),
            'custom_value3'  => $this->faker->numberBetween(1, 4),
            'custom_value4'  => $this->faker->numberBetween(1, 4),
            'column_color'   => $this->faker->colorName,
        ];
    }
}
