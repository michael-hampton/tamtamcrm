<?php

namespace App\Models;

use App\Repositories\CreditRepository;

class Refund
{

    /**
     * @var Payment
     */
    private Payment $payment;

    /**
     * @var CreditRepository
     */
    private CreditRepository $credit_repo;

    private $data;

    /**
     * Refund constructor.
     * @param Payment $payment
     * @param CreditRepository $credit_repo
     * @param array $data
     */
    public function __construct(Payment $payment, CreditRepository $credit_repo, array $data)
    {
        $this->payment = $payment;
        $this->credit_repo = $credit_repo;
        $this->data = $data;
    }
}
