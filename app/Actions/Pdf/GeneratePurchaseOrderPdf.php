<?php

namespace App\Actions\Pdf;


use App\Components\Pdf\PurchaseOrderPdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\PurchaseOrder;

class GeneratePurchaseOrderPdf
{
    private PurchaseOrder $purchase_order;

    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }

    public function execute($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->purchase_order->company->primary_contact()->first();
        }

        return CreatePdf::dispatchNow(
            new PurchaseOrderPdf($this->purchase_order),
            $this->purchase_order,
            $contact,
            $update,
            'purchase_order'
        );
    }
}