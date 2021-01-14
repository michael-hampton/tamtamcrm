<?php

namespace Database\Factories;

use App\Models\Cases;
use App\Models\Customer;
use App\Models\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CasesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cases::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $account = \App\Models\Account::first();
        return [
            'status_id'     => \App\Models\Cases::STATUS_DRAFT,
            'subject'       => $this->faker->word,
            'message'       => $this->faker->sentence,
            'private_notes' => $this->faker->sentence,
            'account_id'    => $account->id,
            'user_id'       => $user->id,
            'customer_id'   => $customer->id,
            'category_id'   => 1,
            'priority_id'   => 1,
            'due_date'      => \Carbon\Carbon::today()->addDays(5),
        ];
    }
}
