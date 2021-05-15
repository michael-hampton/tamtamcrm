<?php

namespace App\Services\Order;

use App\Factory\CloneOrderToInvoiceFactory;
use App\Models\Invoice;
use App\Models\Order;
use App\Repositories\InvoiceRepository;
use ReflectionException;

class ConvertOrder
{
    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * ConvertOrder constructor.
     * @param InvoiceRepository $invoice_repo
     * @param Order $order
     */
    public function __construct(InvoiceRepository $invoice_repo, Order $order)
    {
        $this->invoice_repo = $invoice_repo;
        $this->order = $order;
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    public function execute()
    {
        $invoice = CloneOrderToInvoiceFactory::create($this->order, $this->order->user, $this->order->account);
        $invoice->status_id = Invoice::STATUS_SENT;
        $this->invoice_repo->save([], $invoice);
        $this->invoice_repo->markSent($invoice);

        return $invoice;
    }
}
