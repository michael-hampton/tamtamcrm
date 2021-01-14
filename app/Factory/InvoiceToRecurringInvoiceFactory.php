<?php

namespace App\Factory;

use App\Models\Invoice;
use App\Models\RecurringInvoice;

class InvoiceToRecurringInvoiceFactory
{
    public static function create(Invoice $invoice): RecurringInvoice
    {
        $recurring_invoice = new RecurringInvoice;
        $recurring_invoice->fill($invoice->toArray());
        $recurring_invoice->number = null;
        $recurring_invoice->status_id = RecurringInvoice::STATUS_DRAFT;
        $recurring_invoice->number = '';
        $recurring_invoice->date = date_create()->format('Y-m-d');
        $recurring_invoice->customer_id = $invoice->customer_id;
        $recurring_invoice->due_date = $recurring_invoice->setDueDate();
        $recurring_invoice->total = $invoice->total;
        $recurring_invoice->balance = $invoice->total;
        $recurring_invoice->user_id = $invoice->user_id;
        $recurring_invoice->account_id = $invoice->account_id;
        $recurring_invoice->frequency = 'MONTHLY';

        return $recurring_invoice;
    }
}
