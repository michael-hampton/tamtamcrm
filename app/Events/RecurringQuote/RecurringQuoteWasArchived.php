<?php

namespace App\Events\RecurringQuote;

use App\Models\RecurringQuote;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class RecurringQuoteWasArchived
{
    use SerializesModels;

    /**
     * @var RecurringQuote
     */
    public RecurringQuote $recurringQuote;

    /**
     * RecurringQuoteWasArchived constructor.
     * @param RecurringQuote $recurringQuote
     */
    public function __construct(RecurringQuote $recurringQuote)
    {
        $this->recurringQuote = $recurringQuote;
    }
}
