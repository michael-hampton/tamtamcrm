<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

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
            'user_id'          => $user->id,
            'account_id'       => 1,
            'is_deleted'       => false,
            'amount'           => $this->faker->numberBetween(1, 10),
            'date'             => $this->faker->date(),
            'reference_number' => $this->faker->text(10),
            'type_id'          => 1,
            'status_id'        => Payment::STATUS_COMPLETED,
            'customer_id'      => $customer->id,
        ];
    }
}
