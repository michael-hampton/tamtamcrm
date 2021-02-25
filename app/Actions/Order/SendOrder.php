<?php


namespace App\Actions\Order;


use App\Actions\Email\DispatchEmail;
use App\Components\Shipping\ShippoShipment;
use App\Events\Order\OrderWasDispatched;
use App\Jobs\Inventory\UpdateInventory;
use App\Models\Order;

class SendOrder
{

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function execute()
    {
        // trigger
        $subject = $this->order->customer->getSetting('email_subject_order_sent');
        $body = $this->order->customer->getSetting('email_template_order_sent');

        (new DispatchEmail($this->order))->execute(null, $subject, $body, 'order');

        if (!empty($this->order->shipping_id)) {
            (new ShippoShipment($this->order->customer, $this->order->line_items))->createLabel($this->order);
        }

        if ($this->order->customer->getSetting(
                'inventory_enabled'
            ) === true || $this->order->customer->getSetting('should_update_inventory') === true) {
            UpdateInventory::dispatch($this->order);
        }

        if ($this->order->customer->getSetting(
                'order_charge_point'
            ) === 'on_send' && $this->order->payment_taken === false) {
            (new CompleteOrderPayment($this->order))->execute();
        }

        event(new OrderWasDispatched($this->order));

        return true;
    }
}