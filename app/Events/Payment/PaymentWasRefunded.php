<?php

namespace App\Events\Payment;

use App\Models\Payment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentWasRefunded.
 */
class PaymentWasRefunded
{
    use SerializesModels;

    /**
     * @var Payment
     */
    public $payment;

    public $data;

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     * @param $data
     */
    public function __construct(Payment $payment, $data)
    {
        $this->payment = $payment;
        $this->data = $data;
    }
}
