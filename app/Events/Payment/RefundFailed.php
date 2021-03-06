<?php

namespace App\Events\Payment;

use App\Models\Payment;
use Illuminate\Queue\SerializesModels;

/**
 * Class RefundFailed
 * @package App\Events\Payment
 */
class RefundFailed
{
    use SerializesModels;

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
    }
}
