<?php

namespace App\Rules\Payment;

use App\Models\Credit;
use Illuminate\Contracts\Validation\Rule;

class CreditPaymentValidation implements Rule
{

    private $request;

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

    private function validate(array $arrCredits)
    {
        $credit_total = 0;
        $this->customer = null;
        $arrAddedCredits = [];

        foreach ($arrCredits as $arrCredit) {
            $credit = $this->validateCredit($arrCredit);

            if (!$credit) {
                return false;
            }

            if (!$this->validateCustomer($credit)) {
                return false;
            }

            if (in_array($credit->id, $arrAddedCredits)) {
                return false;
            }

            $arrAddedCredits[] = $credit->id;


            $credit_total += $credit->total;
        }

        return true;
    }

    private function validateCredit(array $arrCredit)
    {
        $credit = Credit::whereId($arrCredit['credit_id'])->first();

        // check allowed statuses here
        if (!$credit || $credit->hide) {
            $this->validationFailures[] = trans('texts.invalid_payment_credit');
            return false;
        }

        if ($credit->balance <= 0 || $arrCredit['amount'] > $credit->balance) {
            $this->validationFailures[] = trans('texts.invalid_payment_amount');
            return false;
        }

        if (!in_array($credit->status_id, [Credit::STATUS_SENT, Credit::STATUS_PARTIAL])) {
            $this->validationFailures[] = trans('texts.invalid_credit_status');
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
            $this->validationFailures[] = trans('texts.invalid_payment_customer');
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
