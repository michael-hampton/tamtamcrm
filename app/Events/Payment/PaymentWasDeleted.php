<?php

namespace App\Events\Payment;

use App\Models\Payment;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentWasDeleted.
 */
class PaymentWasDeleted
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Payment
     */
    public Payment $payment;

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
