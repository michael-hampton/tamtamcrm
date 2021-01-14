<?php

namespace App\Events\Invoice;

use App\Models\Invoice;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasDeleted.
 */
class InvoiceWasDeleted
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Invoice
     */
    public Invoice $invoice;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
