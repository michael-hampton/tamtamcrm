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
    public RecurringInvoice $recurringInvoice;

    /**
     * RecurringInvoiceWasArchived constructor.
     * @param RecurringInvoice $recurringInvoice
     */
    public function __construct(RecurringInvoice $recurringInvoice)
    {
        $this->recurringInvoice = $recurringInvoice;
    }
}
