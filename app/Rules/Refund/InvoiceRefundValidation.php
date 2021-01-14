<?php

namespace App\Rules\Refund;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Paymentable;
use Illuminate\Contracts\Validation\Rule;

class InvoiceRefundValidation implements Rule
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
        if (!isset($this->request['invoices'])) {
            return true;
        }

        if (!$this->validate($this->request['invoices'])) {
            return false;
        }

        return true;
    }

    private function validate(array $arrInvoices): bool
    {
        $invoice_total = 0;
        $this->customer = null;

        foreach ($arrInvoices as $arrInvoice) {
            $invoice = $this->validateInvoice($arrInvoice);

            if (!$invoice) {
                return false;
            }

            if (!$this->validateCustomer($invoice)) {
                return false;
            }

            $invoice_total += $invoice->total;
        }

        if ($this->request['amount'] > $invoice_total) {
            return false;
        }

        return true;
    }

    private function validateInvoice($arrInvoice)
    {
        $invoice = Invoice::whereId($arrInvoice['invoice_id'])->first();

        // check allowed statuses here
        if (!$invoice || $invoice->is_deleted) {
            $this->validationFailures[] = 'Invoice is not a valid invoice';
            return false;
        }

        /*if($invoice->balance <= 0) {
            $this->validationFailures[] = 'The invoice has already been paid';
            return false;
        }*/

        if (!in_array($invoice->status_id, [Invoice::STATUS_PAID, Invoice::STATUS_PARTIAL])) {
            $this->validationFailures[] = 'Invoice is at the wrong status';
            return false;
        }

        $paymentable = Paymentable::where('paymentable_id', $arrInvoice['invoice_id'])->where(
            'payment_id',
            $this->request['id']
        )->where('paymentable_type', get_class($invoice))->first();

        $this->payment = Payment::whereId($this->request['id'])->first();

        if (!$this->payment) {
            return false;
        }

        $allowed_invoices = $this->payment->invoices->pluck('id')->toArray();

        if (!in_array($arrInvoice['invoice_id'], $allowed_invoices)) {
            $this->validationFailures[] = 'Invoice is invalid';
            return false;
        }

        $refundable_amount = ($paymentable->amount - $paymentable->refunded);

        if ($arrInvoice['amount'] > $refundable_amount) {
            return false;
        }

        return $invoice;
    }

    private function validateCustomer(Invoice $invoice)
    {
        if ($this->customer === null) {
            $this->customer = $invoice->customer;
            return true;
        }

        if ($this->customer->id !== $invoice->customer->id) {
            $this->validationFailures[] = 'Cannot create invoice for different customers';
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
