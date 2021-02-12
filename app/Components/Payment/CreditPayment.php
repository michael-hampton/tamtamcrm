<?php


namespace App\Components\Payment;


use App\Models\Credit;
use App\Models\Payment;
use App\Repositories\PaymentRepository;

class CreditPayment extends BasePaymentProcessor
{
    /**
     * @var array|mixed
     */
    private array $credits;

    /**
     * CreditPayment constructor.
     * @param array $data
     * @param Payment $payment
     * @param PaymentRepository $payment_repo
     */
    public function __construct(array $data, Payment $payment, PaymentRepository $payment_repo, bool $applying_existing_payment = false)
    {
        parent::__construct($payment, $payment_repo, $data, $applying_existing_payment);
        $this->credits = $data['credits'];
    }

    /**
     * @return Payment|null
     */
    public function process(): ?Payment
    {
        $credits = Credit::whereIn('id', array_column($this->credits, 'credit_id'))->get();
        $payment_credits = collect($this->credits)->keyBy('credit_id')->toArray();

        foreach ($credits as $credit) {
            if (empty($payment_credits[$credit->id])) {
                continue;
            }

            $amount = $payment_credits[$credit->id]['amount'];
            $this->payment->attachCredit($credit, $amount);
            $this->updateCredits($credit, $amount);
            $this->increasePaymentAmount($amount);
        }

        return $this->payment;
    }

    /**
     * @param Credit $credit
     * @param $amount
     * @return bool
     */
    private function updateCredits(Credit $credit, $amount)
    {
        $credit_balance = $credit->balance;
        $status = $amount == $credit_balance ? Credit::STATUS_APPLIED : Credit::STATUS_PARTIAL;
        $credit->setStatus($status);
        $balance = floatval($amount * -1);
        $credit->increaseBalance($balance);
        $credit->increaseAmountPaid($amount);
        $credit->save();

        return true;
    }
}
