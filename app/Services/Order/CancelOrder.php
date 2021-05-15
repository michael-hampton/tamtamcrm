<?php

namespace App\Services\Order;


use App\Events\Order\OrderWasCancelled;
use App\Jobs\Inventory\ReverseInventory;
use App\Models\Order;

class CancelOrder
{

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function execute()
    {
        if (in_array($this->order->status_id, [Order::STATUS_HELD, Order::STATUS_CANCELLED])) {
            return null;
        }

        $this->order->cacheData();
        $this->order->setStatus(Order::STATUS_CANCELLED);
        $this->order->setDateCancelled();
        $this->order->save();

        $update_reserved_stock = $this->order->status_id !== Order::STATUS_SENT;

        (new ReverseInventory($this->order, $update_reserved_stock));

        event(new OrderWasCancelled($this->order));
        return $this->order;
    }

}