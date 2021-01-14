<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();

        for ($x = 0; $x < 5; $x++) {
            $line_items[] = (new \App\Components\InvoiceCalculator\LineItem)
                ->setQuantity($this->faker->numberBetween(1, 10))
                ->setUnitPrice($this->faker->randomFloat(2, 1, 1000))
                ->calculateSubTotal()->setUnitDiscount($this->faker->numberBetween(1, 10))
                ->setUnitTax(10.00)
                ->setProductId($this->faker->word())
                ->setNotes($this->faker->realText(50))
                ->toObject();
        }

        return [
            'account_id' => 1,
            'status_id' => Quote::STATUS_DRAFT,
            'number' => '',
            'total' => $this->faker->randomFloat(),
            'tax_total' => $this->faker->randomFloat(),
            'discount_total' => $this->faker->randomFloat(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'custom_value1' => $this->faker->numberBetween(1,4),
            'custom_value2' => $this->faker->numberBetween(1,4),
            'custom_value3' => $this->faker->numberBetween(1,4),
            'custom_value4' => $this->faker->numberBetween(1,4),
            'is_deleted' => false,
            'po_number' => $this->faker->text(10),
            'line_items'     => $line_items,
        ];
    }
}
