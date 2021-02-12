<?php

namespace App\Components\Payment;

use App\Actions\Transaction\TriggerTransaction;
use App\Models\Payment;
use App\Repositories\PaymentRepository;

class BasePaymentProcessor
{

    /**
     * @var Payment
     */
    protected Payment $payment;

    /**
     * @var float
     */
    private float $amount = 0;

    private float $gateway_fee = 0;

    private $credited_amount = 0;

    private array $data;


    private PaymentRepository $payment_repo;

    /**
     * @var bool
     */
    private bool $applying_existing_payment = false;


    /**
     * BasePaymentProcessor constructor.
     * @param Payment $payment
     * @param PaymentRepository $payment_repo
     * @param array $data
     * @param bool $applying_existing_payment
     */
    public function __construct(Payment $payment, PaymentRepository $payment_repo, array $data, bool $applying_existing_payment = false)
    {
        $this->payment = $payment;
        $this->payment_repo = $payment_repo;
        $this->data = $data;
        $this->applying_existing_payment = $applying_existing_payment;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return BasePaymentProcessor
     * @return BasePaymentProcessor
     */
    protected function increasePaymentAmount(float $amount)
    {
        if (empty($amount)) {
            return $this;
        }

        $this->amount += $amount;

        return $this;
    }

    protected function setCreditedAmount(float $amount)
    {
        if (empty($amount)) {
            return $this;
        }

        $this->credited_amount += $amount;
        return $this;
    }

    /**
     * @param float $amount
     * @return BasePaymentProcessor
     * @return BasePaymentProcessor
     */
    protected function reducePaymentAmount(float $amount)
    {
        if (empty($amount)) {
            return $this;
        }

        $this->amount -= $amount;
        return $this;
    }

    protected function setGatewayFee(float $gateway_fee)
    {
        $this->gateway_fee += $gateway_fee;
    }

    protected function save(): ?Payment
    {
        $this->applyPayment();
        $this->setStatus();
        $this->updateCustomer();

        $this->payment->save();

        return $this->payment;
        //event(new PaymentWasRefunded($this->payment, $this->data));
    }

    private function applyPayment()
    {
        if ($this->amount > $this->payment->amount) {
            return true;
        }

        //TODO - Need to check this

        // if the payment already has an amount keep the original amount
        if (!$this->applying_existing_payment) {
            $this->payment->amount = $this->amount;
        }

        $this->payment->applied += $this->amount;

        if ($this->gateway_fee > 0) {
            $this->payment->amount += $this->gateway_fee;
            $this->payment->applied += $this->gateway_fee;
            $this->amount += $this->gateway_fee;
        }

        if ($this->credited_amount > 0) {
            //$this->payment->amount += $this->credited_amount;
            $this->payment->applied += $this->credited_amount;
            //$this->amount += $this->gateway_fee;
        }
        //$this->payment->save();

        return true;
    }

    /**
     * @return mixed
     */
    private function updateCustomer()
    {
        if (isset($payment->id)) {
            return true;
        }

        $amount = $this->amount == 0 ? ($this->data['amount'] + $this->gateway_fee) : $this->amount;
        $customer = $this->payment->customer;

        $customer->increaseAmountPaid($amount);
        $customer->reduceBalance($amount);
        $customer->save();

        (new TriggerTransaction($this->payment))->execute(
            $this->amount * -1,
            $customer->balance,
            "Customer Payment {$this->payment->number}"
        );

        return $this;
    }

    private function setStatus()
    {
        if($this->payment->applied < $this->payment->amount) {
            $this->payment->setStatus(Payment::STATUS_PENDING);
        }
        //$status = $this->payment->refunded == $this->amount ? Payment::STATUS_REFUNDED : Payment::STATUS_PARTIALLY_REFUNDED;
        //$this->payment->setStatus($status);
    }
}
