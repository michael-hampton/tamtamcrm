<?php

namespace App\Observers;

use App\Actions\Order\FulfilOrder;
use App\Actions\Order\HoldStock;
use App\Actions\Order\UpdateInventory;
use App\Events\Order\OrderWasBackordered;
use App\Models\Order;
use App\Traits\BuildVariables;
use App\Traits\Money;

class OrderObserver
{
    use BuildVariables;
    use Money;

    /**
     * Handle the Order "created" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function created(Order $order)
    {
        if ($order->customer->getSetting('inventory_enabled') === true) {
            $order = (new FulfilOrder($order))->execute();

            /************** hold stock ***************************/
            // if the order hasnt been failed at this point then reserve stock
            if ($order->status_id !== Order::STATUS_ORDER_FAILED) {
                (new HoldStock($order))->execute();
            }

            $order->save();
        }

        // send backorder notification if order has been backordered
        if ($order->status_id === Order::STATUS_BACKORDERED) {
            event(new OrderWasBackordered($order));
        }
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function updated(Order $order)
    {
        if (!empty($order->getOriginal())) {
            $original_order = $order->getOriginal();

            $line_items = $original_order['line_items'];

            (new UpdateInventory($order))->updateInventory($line_items, $order);
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }

    public function saving(Order $order)
    {
    }
}
