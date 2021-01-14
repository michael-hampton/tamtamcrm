<?php

namespace App\Events\PurchaseOrder;

use App\Models\PurchaseOrder;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class PurchaseOrderWasRejected
{
    use SerializesModels;

    /**
     * @var PurchaseOrder
     */
    public PurchaseOrder $purchase_order;

    /**
     * PurchaseOrderWasApproved constructor.
     * @param PurchaseOrder $purchase_order
     */
    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }
}
