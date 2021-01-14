<?php

namespace App\Events\Payment;

use App\Models\Payment;
use App\Traits\SendSubscription;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use robertogallea\LaravelMetrics\Models\Interfaces\PerformsMetrics;
use robertogallea\LaravelMetrics\Models\Traits\Measurable;

/**
 * Class PaymentWasUpdated.
 */
class PaymentWasUpdated implements PerformsMetrics
{
    use SerializesModels;
    use Dispatchable;
    use Measurable;
    use SendSubscription;

    /**
     * @var Payment
     */
    public Payment $payment;
    protected $meter = 'payment-updated';

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->send($payment, get_class($this));
    }
}
