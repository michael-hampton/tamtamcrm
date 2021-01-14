<?php

namespace App\Events\Payment;

use App\Models\Payment;
use Illuminate\Queue\SerializesModels;

class PaymentWasEmailedAndFailed
{
    use SerializesModels;

    /**
     * @var Payment
     */
    public $payment;

    /**
     * @var array
     */
    public $errors;

    /**
     * PaymentWasEmailedAndFailed constructor.
     * @param Payment $payment
     * @param array $errors
     */
    public function __construct(Payment $payment, array $errors)
    {
        $this->payment = $payment;

        $this->errors = $errors;
    }
}
