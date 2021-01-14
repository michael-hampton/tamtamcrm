<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();

        for ($x = 0; $x < 2; $x++) {
            $product = Product::factory()->create();

            $line_items[] = (new \App\Components\InvoiceCalculator\LineItem)
                ->setQuantity(2)
                ->setUnitPrice(40)
                ->calculateSubTotal()->setUnitDiscount(0)
                ->setUnitTax(0)
                ->setProductId($product->id)
                ->setNotes($this->faker->realText(50))
                ->toObject();
        }

        return [
            'account_id' => 1,
            'status_id' => Order::STATUS_DRAFT,
            'number' => '',
            'total' => 160,
            'tax_total' => 20,
            'shipping_cost' => 20,
            'discount_total' => 20,
            'customer_id' => 5,
            'user_id' => $user->id,
            'date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'custom_value1' => $this->faker->numberBetween(1,4),
            'custom_value2' => $this->faker->numberBetween(1,4),
            'custom_value3' => $this->faker->numberBetween(1,4),
            'custom_value4' => $this->faker->numberBetween(1,4),
            'is_deleted' => false,
            'po_number' => $this->faker->text(10),
            'line_items' => $line_items,
            'gateway_fee' => 0
        ];
    }
}
