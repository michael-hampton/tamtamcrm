<?php

namespace App\Actions\Order;


use App\Models\Order;

class ReverseStatus
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function execute(): ?Order
    {
        if ($this->order->status_id !== Order::STATUS_HELD) {
            return null;
        }

        $this->order->rewindCache();

        return $this->order->fresh();
    }
}