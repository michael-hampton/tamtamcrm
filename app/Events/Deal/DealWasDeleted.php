<?php

namespace App\Events\Deal;

use App\Models\Deal;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class DealWasDeleted
{
    use SerializesModels;
    use SendSubscription;

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
        $this->send($deal, get_class($this));
    }
}
