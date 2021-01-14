<?php


namespace App\Components\Refund;


use App\Models\Credit;
use App\Models\Payment;
use App\Repositories\CreditRepository;

class CreditRefund extends BaseRefund
{
    private array $payment_credits;

    /**
     * CreditRefund constructor.
     * @param Payment $payment
     * @param array $data
     * @param CreditRepository $credit_repo
     * @param array $payment_credits
     */
    public function __construct(Payment $payment, array $data, CreditRepository $credit_repo, $payment_credits)
    {
        parent::__construct($payment, $data, $credit_repo);
        $this->payment_credits = $payment_credits;
    }

    public function refund()
    {
        $credits = $this->payment->credits->keyBy('id');

        foreach ($this->payment_credits as $payment_credit) {
            $total = $payment_credit['amount'];

            $credit_id = $payment_credit['credit_id'];

            if (empty($credits[$credit_id])) {
                return false;
            }

            $credit = $credits[$credit_id];

            $available_credit = $payment_credit['amount'] - $credit->pivot->refunded;

            $total_to_credit = $available_credit > $total ? $total : $available_credit;
            $this->updateRefundedAmountForCredit($credit, $total_to_credit);
            $this->updateCreditNote($credit, $total_to_credit);
            $this->increaseRefundAmount($available_credit <= $total ? $available_credit : 0);
        }

        $this->completeCreditRefund();

        return $this->payment;
    }

    /**
     * @param $credit
     * @param $amount
     * @return bool
     */
    private function updateRefundedAmountForCredit($credit, $amount): bool
    {
        $credit->pivot->refunded += $amount;
        $credit->pivot->save();

        return true;
    }

    private function updateCreditNote($credit, $amount)
    {
        $credit->increaseBalance($amount);
        $credit->setStatus(Credit::STATUS_SENT);
        $credit->save();

        $credit->transaction_service()->createTransaction($amount, $credit->customer->balance);
        return true;
    }
}
