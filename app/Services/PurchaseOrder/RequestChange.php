<?php

namespace App\Services\PurchaseOrder;


use App\Events\PurchaseOrder\PurchaseOrderChangeWasRequested;
use App\Models\PurchaseOrder;
use App\Repositories\PurchaseOrderRepository;

class RequestChange
{

    private PurchaseOrder $purchase_order;

    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }

    /**
     * @param PurchaseOrderRepository $po_repo
     * @param array $data
     * @return PurchaseOrder|null
     */
    public function execute(PurchaseOrderRepository $po_repo, array $data = []): ?PurchaseOrder
    {
        if ($this->purchase_order->status_id != PurchaseOrder::STATUS_SENT) {
            return null;
        }

        if (!empty($data['customer_note'])) {
            $this->purchase_order->customer_note = $data['customer_note'];
        }

        $this->purchase_order->setStatus(PurchaseOrder::STATUS_CHANGE_REQUESTED);
        //$this->purchase_order->date_approved = Carbon::now();
        $this->purchase_order->save();

        event(new PurchaseOrderChangeWasRequested($this->purchase_order));

        // trigger
        $subject = trans('texts.purchase_order_change_requested_subject');
        $body = trans('texts.purchase_order_change_requested_body');
        $this->trigger($subject, $body, $po_repo);

        return $this->purchase_order;
    }
}