<?php

namespace App\Actions\Order;


use App\Events\Order\OrderWasHeld;
use App\Models\Order;

class HoldOrder
{

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Order
     */
    public function execute(): ?Order
    {
        if ($this->order->status_id === Order::STATUS_HELD) {
            return null;
        }

        $this->order->cacheData();
        $this->order->setStatus(Order::STATUS_HELD);
        $this->order->save();

        event(new OrderWasHeld($this->order));
        return $this->order;
    }
}