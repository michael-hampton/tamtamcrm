<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasDeleted.
 */
class OrderWasDeleted
{
    use SerializesModels;
    use SendSubscription;

    public Order $order;

    /**
     * Create a new event instance.
     *
     * @param $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->send($order, get_class($this));
    }
}
