<?php

namespace App\Events\Quote;

use App\Models\Quote;
use Illuminate\Queue\SerializesModels;

class QuoteWasArchived
{
    use SerializesModels;

    public Quote $quote;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }
}
