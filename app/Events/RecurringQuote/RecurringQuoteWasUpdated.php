<?php

namespace App\Events\RecurringQuote;

use App\Models\RecurringQuote;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class RecurringQuoteWasUpdated
{
    use SerializesModels;

    /**
     * @var RecurringQuote
     */
    public RecurringQuote $recurring_quote;

    /**
     * RecurringQuoteWasUpdated constructor.
     * @param RecurringQuote $recurring_quote
     */
    public function __construct(RecurringQuote $recurring_quote)
    {
        $this->recurring_quote = $recurring_quote;
    }
}
