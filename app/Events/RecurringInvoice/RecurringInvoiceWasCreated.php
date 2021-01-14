<?php

namespace App\Events\RecurringInvoice;

use App\Models\RecurringInvoice;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasCreated.
 */
class RecurringInvoiceWasCreated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var RecurringInvoice
     */
    public RecurringInvoice $recurring_invoice;

    /**
     * RecurringInvoiceWasCreated constructor.
     * @param RecurringInvoice $recurring_invoice
     */
    public function __construct(RecurringInvoice $recurring_invoice)
    {
        $this->recurring_invoice = $recurring_invoice;
        $this->send($recurring_invoice, get_class($this));
    }
}
