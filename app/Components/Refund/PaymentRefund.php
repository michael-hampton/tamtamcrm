<?php

namespace App\Components\Refund;

use App\Models\Payment;
use App\Repositories\CreditRepository;

class PaymentRefund extends BaseRefund
{

    /**
     * PaymentRefund constructor.
     * @param Payment $payment
     * @param array $data
     * @param CreditRepository $credit_repo
     */
    public function __construct(Payment $payment, array $data, CreditRepository $credit_repo)
    {
        parent::__construct($payment, $data, $credit_repo);
    }

    /**
     * @return Payment
     */
    public function refund()
    {
        //adjust payment refunded column amount
        $this->increaseRefundAmount($this->data['amount']);
        $this->createLineItem($this->data['amount']);
        $this->save();

        return $this->payment;
    }
}