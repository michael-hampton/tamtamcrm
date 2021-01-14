<?php

namespace Database\Factories;

use App\Models\PaymentTerms;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTermsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentTerms::class;

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
            'name' => $this->faker->unique()->word,
            'user_id' => $user->id
        ];
    }
}
