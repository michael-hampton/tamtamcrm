<?php

namespace App\Events\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasDeleted.
 */
class PurchaseOrderWasDeleted
{
    use SerializesModels;
    use SendSubscription;

    public PurchaseOrder $purchase_order;

    /**
     * Create a new event instance.
     *
     * @param $purchase_order
     */
    public function __construct(PurchaseOrder $purchase_order)
    {
        $this->purchase_order = $purchase_order;
        $this->send($purchase_order, get_class($this));
    }
}
