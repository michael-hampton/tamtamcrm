<?php

namespace App\Events\Quote;

use App\Models\Quote;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasCreated.
 */
class QuoteWasCreated
{
    use SerializesModels;
    use SendSubscription;

    public Quote $quote;

    /**
     * QuoteWasCreated constructor.
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
        $this->send($quote, get_class($this));
    }
}
