<?php

namespace App\Events\PurchaseOrder;

use App\Models\PurchaseOrder;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderWasArchived
{
    use SerializesModels;

    public PurchaseOrder $purchase_order;

    /**
     * PurchaseOrderWasArchived constructor.
     * @param PurchaseOrder $purchase_order
     */
    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }
}
