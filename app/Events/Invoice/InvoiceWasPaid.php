<?php

namespace App\Events\Invoice;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasPaid.
 */
class InvoiceWasPaid
{
    use SerializesModels;

    /**
     * @var Invoice
     */
    public Invoice $invoice;

    /**
     * @var Payment
     */
    public Payment $payment;

    /**
     * InvoiceWasPaid constructor.
     * @param Invoice $invoice
     * @param Payment $payment
     */
    public function __construct(Invoice $invoice, Payment $payment)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
    }
}
