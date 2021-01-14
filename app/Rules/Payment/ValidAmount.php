<?php

namespace App\Rules\Payment;

use Illuminate\Contracts\Validation\Rule;

class ValidAmount implements Rule
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
        return $this->validate();
    }

    private function validate()
    {
        $invoice_total = 0;
        $credit_total = 0;
        $total = 0;

        if (!empty($this->request['invoices'])) {
            $invoice_total += array_sum(array_column($this->request['invoices'], 'amount'));
            $total += $invoice_total;
        }

        if (!empty($this->request['credits'])) {
            $credit_total += array_sum(array_column($this->request['credits'], 'amount'));
            $total += $credit_total;
        }

        if ($invoice_total <= 0) {
            return false;
        }

        if ($credit_total > 0 && $credit_total > $invoice_total) {
            return false;
        }

        return $this->request['amount'] <= $total;
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
