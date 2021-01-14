<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\RecurringInvoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurringInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RecurringInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();

        $total = 800;

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
            'account_id'     => 1,
            'status_id'      => \App\Models\RecurringInvoice::STATUS_DRAFT,
            'number'         => $this->faker->ean13(),
            'total'          => $total,
            'balance'        => $total,
            'tax_total'      => $this->faker->randomFloat(2),
            'discount_total' => $this->faker->randomFloat(2),
            'customer_id'    => $customer->id,
            'user_id'        => $user->id,
            'is_deleted'     => false,
            'po_number'      => $this->faker->text(10),
            'date'           => $this->faker->date(),
            'due_date'       => $this->faker->date(),
            'line_items'     => $line_items,
            'terms'          => $this->faker->text(500),
            'frequency'      => 'MONTHLY',
            'start_date'     => $this->faker->date(),
            'expiry_date'    => $this->faker->date()
        ];
    }
}
