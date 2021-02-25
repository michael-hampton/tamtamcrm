<?php

namespace App\Actions\Order;


use App\Models\Order;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use ReflectionException;

class DispatchOrder
{

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param InvoiceRepository $invoice_repo
     * @param OrderRepository $order_repo
     * @param bool $force_invoice
     * @return Order
     * @throws ReflectionException
     */
    public function execute(
        InvoiceRepository $invoice_repo,
        OrderRepository $order_repo,
        $force_invoice = false
    ): Order {
        if ($this->order->customer->getSetting('should_convert_order') || $force_invoice === true) {
            $invoice = (new ConvertOrder($invoice_repo, $this->order))->execute();
            $this->order->setInvoiceId($invoice->id);
            $this->order->save();
        }

        return $this->order;
    }
}