<?php

namespace App\Events\Quote;

use App\Models\Quote;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasDeleted.
 */
class QuoteWasDeleted
{
    use SerializesModels;
    use SendSubscription;

    public Quote $quote;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
        $this->send($quote, get_class($this));
    }
}
