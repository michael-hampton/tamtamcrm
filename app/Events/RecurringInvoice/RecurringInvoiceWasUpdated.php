<?php

namespace App\Events\RecurringInvoice;

use App\Models\RecurringInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class RecurringInvoiceWasUpdated
{
    use SerializesModels;

    /**
     * @var RecurringInvoice
     */
    public RecurringInvoice $recurring_invoice;

    /**
     * RecurringInvoiceWasUpdated constructor.
     * @param RecurringInvoice $recurring_invoice
     */
    public function __construct(RecurringInvoice $recurring_invoice)
    {
        $this->recurring_invoice = $recurring_invoice;
    }
}
