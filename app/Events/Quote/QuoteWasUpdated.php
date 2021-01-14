<?php

namespace App\Events\Quote;

use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasUpdated.
 */
class QuoteWasUpdated
{
    use SerializesModels;
    use SendSubscription;

    public $quote;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct($quote)
    {
        $this->quote = $quote;
        $this->send($quote, get_class($this));
    }
}
