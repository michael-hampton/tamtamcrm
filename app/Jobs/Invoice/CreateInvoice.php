<?php

namespace App\Jobs\Invoice;

use App\Components\InvoiceCalculator\LineItem;
use App\Factory\InvoiceFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Repositories\InvoiceRepository;
use App\Traits\CreditPayment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CreditPayment;

    private Customer $customer;

    private Account $account;

    private Product $product;

    private array $data;

    public function __construct(Account $account, Customer $customer, Product $product, array $data)
    {
        $this->account = $account;
        $this->customer = $customer;
        $this->product = $product;
        $this->data = $data;
    }

    public function handle()
    {
        $this->generateInvoice();
    }

    private function generateInvoice(): ?Invoice
    {
        $invoice = InvoiceFactory::create($this->account, $this->customer->user, $this->customer);

        $data = [
            'date' => Carbon::now()
        ];

        $data['line_items'][] = (new LineItem)
            ->setQuantity(!empty($this->data['quantity']) ? $this->data['quantity'] : 1)
            ->setUnitPrice($this->product->price)
            ->calculateSubTotal()
            ->setUnitDiscount(0)
            ->setUnitTax(0)
            ->setProductId($this->product->id)
            ->setNotes($this->product->description)
            ->toObject();


        $invoice = (new InvoiceRepository(new Invoice()))->createInvoice($data, $invoice);

        return $invoice;
    }
}
