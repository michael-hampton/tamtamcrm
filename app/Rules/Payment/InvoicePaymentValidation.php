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
                $this->validationFailures[] = 'Invalid invoice';
                return false;
            }

            if (in_array($invoice->id, $arrAddedInvoices)) {
                $this->validationFailures[] = 'Duplicate invoice found';
                return false;
            }

            if (!$this->validateCustomer($invoice)) {
                $this->validationFailures[] = 'Invalid customer';
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
        if (!$invoice || $invoice->is_deleted) {
            $this->validationFailures[] = 'Invoice is not a valid invoice';
            return false;
        }

        if ($invoice->balance <= 0) {
            $this->validationFailures[] = 'The invoice has already been paid';
            return false;
        }

        if (!in_array($invoice->status_id, [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL])) {
            $this->validationFailures[] = 'Invoice is at the wrong status';
            return false;
        }

        if ($invoice->balance <= 0 || $arrInvoice['amount'] > $invoice->balance) {
            $this->validationFailures[] = 'Payment amount cannot be more that the invoice total';
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
