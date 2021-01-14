<?php

namespace Database\Factories;

use App\Models\Design;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DesignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Design::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();

        return [
            'account_id' => 1,
            'user_id' => $user->id,
            'is_deleted' => false,
            'name' => $this->faker->firstName,
            'design' => '<HTML>test</HTML>'
        ];
    }
}
