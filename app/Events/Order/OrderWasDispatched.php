<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use robertogallea\LaravelMetrics\Models\Interfaces\PerformsMetrics;
use robertogallea\LaravelMetrics\Models\Traits\Measurable;

/**
 * Class InvoiceWasMarkedSent.
 */
class OrderWasDispatched implements PerformsMetrics
{
    use SerializesModels;
    use Dispatchable;
    use Measurable;

    /**
     * @var Order
     */
    public Order $order;
    protected $meter = 'order-dispatched';

    /**
     * Create a new event instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
