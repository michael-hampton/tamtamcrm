<?php

namespace App\Events\Invoice;

use App\Models\Invoice;
use App\Traits\SendSubscription;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use robertogallea\LaravelMetrics\Models\Interfaces\PerformsMetrics;
use robertogallea\LaravelMetrics\Models\Traits\Measurable;

/**
 * Class InvoiceWasCreated.
 */
class InvoiceWasCreated implements PerformsMetrics
{
    use SerializesModels;
    use Dispatchable;
    use Measurable;
    use SendSubscription;

    /**
     * @var Invoice
     */
    public Invoice $invoice;
    protected $meter = 'invoice-created';

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->send($invoice, get_class($this));
    }
}
