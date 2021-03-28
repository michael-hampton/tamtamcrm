<?php

namespace App\Events\RecurringInvoice;

use App\Models\RecurringInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class RecurringInvoiceWasArchived
 * @package App\Events\Cases
 */
class RecurringInvoiceWasArchived
{
    use SerializesModels;

    /**
     * @var RecurringInvoice
     */
    public RecurringInvoice $recurring_invoice;

    /**
     * RecurringInvoiceWasArchived constructor.
     * @param RecurringInvoice $recurring_invoice
     */
    public function __construct(RecurringInvoice $recurring_invoice)
    {
        $this->recurring_invoice = $recurring_invoice;
    }
}
