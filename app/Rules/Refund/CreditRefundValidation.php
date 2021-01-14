<?php

namespace App\Rules\Refund;

use App\Models\Credit;
use App\Models\Payment;
use App\Models\Paymentable;
use Illuminate\Contracts\Validation\Rule;

class CreditRefundValidation implements Rule
{
    private $request;

    private Payment $payment;

    /**
     * @var array
     */
    private $validationFailures = [];

    /**
     * Create a new rule instance.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!isset($this->request['credits'])) {
            return true;
        }

        if (!$this->validate($this->request['credits'])) {
            return false;
        }

        return true;
    }

    private function validate(array $arrCredits): bool
    {
        $credit_total = 0;
        $this->customer = null;

        foreach ($arrCredits as $arrCredit) {
            $credit = $this->validateCredit($arrCredit);

            if (!$credit) {
                return false;
            }

            if (!$this->validateCustomer($credit)) {
                return false;
            }

            $credit_total += $credit->total;
        }

        if ($credit_total > $this->request['amount']) {
            return false;
        }

        return true;
    }

    private function validateCredit($arrCredit)
    {
        $credit = Credit::whereId($arrCredit['credit_id'])->first();

        // check allowed statuses here
        if (!$credit || $credit->is_deleted) {
            $this->validationFailures[] = 'Invoice is not a valid invoice';
            return false;
        }

        /*if($invoice->balance <= 0) {
            $this->validationFailures[] = 'The invoice has already been paid';
            return false;
        }*/

        if (!in_array($credit->status_id, [Credit::STATUS_APPLIED])) {
            $this->validationFailures[] = 'Credit is at the wrong status';
            return false;
        }

        $paymentable = Paymentable::where('paymentable_id', $arrCredit['credit_id'])->where(
            'payment_id',
            $this->request['id']
        )->where('paymentable_type', get_class($credit))->first();

        $this->payment = Payment::whereId($this->request['id'])->first();

        if (!$this->payment) {
            return false;
        }

        $allowed_credits = $this->payment->credits->pluck('id')->toArray();

        if (!in_array($arrCredit['credit_id'], $allowed_credits)) {
            $this->validationFailures[] = 'Credit is invalid';
            return false;
        }

        $refundable_amount = ($paymentable->amount - $paymentable->refunded);

        if ($arrCredit['amount'] > $refundable_amount) {
            return false;
        }

        return $credit;
    }

    private function validateCustomer(Credit $credit)
    {
        if ($this->customer === null) {
            $this->customer = $credit->customer;
            return true;
        }

        if ($this->customer->id !== $credit->customer->id) {
            $this->validationFailures[] = 'Cannot create credit for different customers';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message()
    {
        return $this->validationFailures;
    }
}
