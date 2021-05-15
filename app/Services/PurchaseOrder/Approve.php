<?php

namespace App\Services\PurchaseOrder;


use App\Services\Email\DispatchEmail;
use App\Events\PurchaseOrder\PurchaseOrderWasApproved;
use App\Models\PurchaseOrder;
use App\Repositories\PurchaseOrderRepository;
use Carbon\Carbon;

class Approve
{
    private PurchaseOrder $purchase_order;

    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }

    public function execute(PurchaseOrderRepository $po_repo): ?PurchaseOrder
    {
        if ($this->purchase_order->status_id != PurchaseOrder::STATUS_SENT) {
            return null;
        }

        $this->purchase_order->setStatus(PurchaseOrder::STATUS_APPROVED);
        $this->purchase_order->date_approved = Carbon::now();
        $this->purchase_order->save();

        event(new PurchaseOrderWasApproved($this->purchase_order));

        // trigger
        $subject = trans('texts.purchase_order_approved_subject');
        $body = trans('texts.purchase_order_approved_body');
        (new DispatchEmail($this->purchase_order))->execute(null, $subject, $body);

        if (!empty($this->purchase_order->account->settings->should_archive_purchase_order)) {
            $this->purchase_order->archive();
        }

        return $this->purchase_order;
    }
}