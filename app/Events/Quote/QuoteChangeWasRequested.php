<?php

namespace App\Events\Quote;

use App\Models\Quote;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class QuoteChangeWasRequested
{
    use SerializesModels;

    /**
     * @var Quote
     */
    public Quote $quote;

    /**
     * Create a new event instance.
     *
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }
}
