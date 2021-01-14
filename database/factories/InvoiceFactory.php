<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

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
                ->setQuantity(1)
                ->setUnitPrice(160)
                ->calculateSubTotal()
                ->setUnitDiscount(0)
                ->setUnitTax(0)
                ->setProductId($this->faker->word())
                ->setNotes($this->faker->realText(50))
                ->toObject();
        }

        return [
            'account_id'     => 1,
            'status_id'      => Invoice::STATUS_DRAFT,
            'number'         => $this->faker->ean13(),
            'total'          => $total,
            'balance'        => $total,
            'tax_total'      => 0,
            'discount_total' => 0,
            'customer_id'    => $customer->id,
            'user_id'        => $user->id,
            'is_deleted'     => false,
            'po_number'      => $this->faker->text(10),
            'date'           => \Carbon\Carbon::now()->format('Y-m-d'),
            'due_date'       => \Carbon\Carbon::now()->addDays(3)->format('Y-m-d'),
            'line_items'     => $line_items,
            'terms'          => $this->faker->text(500),
            //'gateway_fee'    => 12.99
        ];
    }
}
