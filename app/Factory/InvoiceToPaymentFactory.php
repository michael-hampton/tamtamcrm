<?php

namespace App\Factory;

use App\Models\Invoice;
use App\Models\Payment;

class InvoiceToPaymentFactory
{
    public static function create(Invoice $invoice): Payment
    {
        $payment = new Payment;

        $payment->account_id = $invoice->account_id;
        $payment->user_id = $invoice->user_id;
        $payment->amount = $invoice->balance;
        $payment->applied = $invoice->balance;
        $payment->customer_id = $invoice->customer_id;
        $payment->reference_number = trans('texts.manual');

        return $payment;
    }
}
