<?php

namespace App\Rules\Refund;

use App\Models\Payment;
use App\Paymentables;
use Illuminate\Contracts\Validation\Rule;

class RefundValidation implements Rule
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
        if (!isset($this->request['id'])) {
            return false;
        }

        if (!$this->validatePayment()) {
            return false;
        }

        return true;
    }


    private function validatePayment()
    {
        $payment = Payment::whereId($this->request['id'])->first();

        if (!$payment) {
            $this->validationFailures[] = 'Invalid payment';
            return false;
        }

        $invoice_total = array_sum(array_column($this->request['invoices'], 'amount'));

        if (!empty($this->request['credits'])) {
            $credit_total = array_sum(array_column($this->request['credits'], 'amount'));
            $invoice_total -= $credit_total;
        }

        if ($invoice_total > $payment->amount) {
            $this->validationFailures[] = 'Refund amount is to high';
            return false;
        }

        if (!in_array($payment->status_id, [Payment::STATUS_COMPLETED, Payment::STATUS_PARTIALLY_REFUNDED])) {
            $this->validationFailures[] = 'payment has not been completed';
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
