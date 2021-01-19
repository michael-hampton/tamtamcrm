<?php

namespace Database\Factories;

use App\Models\CaseCategory;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CaseCategory::class;

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
            'name'         => $this->faker->name,
            'column_color' => $this->faker->colorName
        ];
    }
}
