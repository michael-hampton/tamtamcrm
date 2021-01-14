<?php

namespace App\Events\PurchaseOrder;

use App\Models\PurchaseOrder;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasRestored.
 */
class PurchaseOrderWasRestored
{
    use SerializesModels;

    public PurchaseOrder $purchase_order;

    /**
     * Create a new event instance.
     *
     * @param $purchase_order
     */
    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }
}
