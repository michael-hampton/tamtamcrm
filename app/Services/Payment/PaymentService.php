<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Services\ServiceBase;

class PaymentService extends ServiceBase
{
    /**
     * @var Payment
     */
    private Payment $payment;

    /**
     * PaymentService constructor.
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        parent::__construct($payment);
        $this->payment = $payment;
    }

    public function sendEmail()
    {
        return (new PaymentEmail($this->payment))->execute();
    }

    public function generatePdf()
    {
        //TODO
    }
}
