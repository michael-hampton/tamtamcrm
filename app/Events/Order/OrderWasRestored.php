<?php

namespace App\Events\Order;

use Illuminate\Queue\SerializesModels;

/**
 * Class OrderWasRestored.
 */
class OrderWasRestored
{
    use SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }
}
