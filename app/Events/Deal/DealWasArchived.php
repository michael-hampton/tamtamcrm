<?php

namespace App\Events\Deal;

use App\Models\Deal;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class DealWasArchived
{
    use SerializesModels;

    /**
     * @var deal
     */
    public Deal $deal;

    /**
     * Create a new event instance.
     *
     * @param deal $deal
     */
    public function __construct(Deal $deal)
    {
        $this->deal = $deal;
    }
}
