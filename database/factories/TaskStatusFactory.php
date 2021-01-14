<?php

namespace Database\Factories;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        
        return [
            'user_id'      => $user->id,
            'account_id'   => 1,
            'name'         => $this->faker->word,
            'column_color' => $this->faker->hexColor,
            'task_type'    => 1,
            'description'  => $this->faker->sentence,
            'icon'         => $this->faker->word
        ];
    }
}
