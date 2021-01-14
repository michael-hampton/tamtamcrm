<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();

        return [
            'amount'           => $this->faker->numberBetween(1, 10),
            'account_id'       => 1,
            'user_id'          => $user->id,
            'custom_value1'    => $this->faker->text(10),
            'custom_value2'    => $this->faker->text(10),
            'custom_value3'    => $this->faker->text(10),
            'custom_value4'    => $this->faker->text(10),
            'exchange_rate'    => $this->faker->randomFloat(2, 0, 1),
            'date'             => $this->faker->date(),
            'is_deleted'       => false,
            'public_notes'     => $this->faker->text(50),
            'private_notes'    => $this->faker->text(50),
            'reference_number' => $this->faker->text(5),
        ];
    }
}
