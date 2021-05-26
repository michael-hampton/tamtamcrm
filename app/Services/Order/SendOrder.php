<?php


namespace App\Services\Order;


use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository;
use App\Services\Email\DispatchEmail;
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
        $template = (new EmailTemplateRepository(new EmailTemplate()))->getTemplateForType('order_sent');

        (new DispatchEmail($this->order))->execute(null, $template->subject, $template->message, 'order');

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