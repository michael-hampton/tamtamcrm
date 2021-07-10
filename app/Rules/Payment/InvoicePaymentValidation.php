<?php

namespace App\Rules\Payment;

use App\Models\Invoice;
use Illuminate\Contracts\Validation\Rule;

class InvoicePaymentValidation implements Rule
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
        if (empty($this->request['invoices'])) {
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
        $arrAddedInvoices = [];

        foreach ($arrInvoices as $arrInvoice) {
            if (empty($arrInvoice['invoice_id'])) {
                continue;
            }

            $invoice = $this->validateInvoice($arrInvoice);

            if (!$invoice) {
                $this->validationFailures[] = trans('texts.invalid_payment_invoice');
                return false;
            }

            if (in_array($invoice->id, $arrAddedInvoices)) {
                $this->validationFailures[] = trans('texts.duplicate_invoice');
                return false;
            }

            if (!$this->validateCustomer($invoice)) {
                $this->validationFailures[] = trans('texts.invalid_customer');
                return false;
            }

            $arrAddedInvoices[] = $invoice->id;


            $invoice_total += $invoice->total;
        }

//        if ($invoice_total > $this->request['amount']) {
//            $this->validationFailures[] = 'Payment amount cannot be more that the invoice total';
//            return false;
//        }

        return true;
    }

    private function validateInvoice(array $arrInvoice)
    {
        $invoice = Invoice::whereId($arrInvoice['invoice_id'])->first();

        // check allowed statuses here
        if (!$invoice || $invoice->hide) {
            $this->validationFailures[] = trans('texts.invalid_invoice');
            return false;
        }

        if ($invoice->balance <= 0) {
            $this->validationFailures[] = trans('texts.invoice_already_paid');
            return false;
        }

        if (!in_array($invoice->status_id, [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL])) {
            $this->validationFailures[] = trans('texts.invalid_invoice_status');
            return false;
        }

        if ($invoice->balance <= 0 || $arrInvoice['amount'] > $invoice->balance) {
            $this->validationFailures[] = trans('texts.payment_amount_more_than_invoice_total');
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
