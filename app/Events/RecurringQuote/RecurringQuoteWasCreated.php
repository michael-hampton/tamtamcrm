<?php

namespace App\Events\RecurringQuote;

use App\Models\RecurringQuote;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasCreated.
 */
class RecurringQuoteWasCreated
{
    use SerializesModels;
    use SendSubscription;

    public RecurringQuote $recurring_quote;

    /**
     * Create a new event instance.
     *
     * @param RecurringQuote $recurring_quote
     */
    public function __construct(RecurringQuote $recurring_quote)
    {
        $this->recurring_quote = $recurring_quote;
        $this->send($recurring_quote, get_class($this));
    }
}
