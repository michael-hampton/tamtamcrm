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
        if (!$credit || $credit->is_deleted) {
            $this->validationFailures[] = 'Credit is not valid';
            return false;
        }

        if ($credit->balance <= 0 || $arrCredit['amount'] > $credit->balance) {
            $this->validationFailures[] = 'Payment amount cannot be more that the invoice total';
            return false;
        }

        if (!in_array($credit->status_id, [Credit::STATUS_SENT, Credit::STATUS_PARTIAL])) {
            $this->validationFailures[] = 'Credit is at the wrong status';
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
            $this->validationFailures[] = 'Cannot create payment for different customers';
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
