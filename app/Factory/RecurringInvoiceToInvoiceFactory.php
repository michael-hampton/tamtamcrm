<?php

namespace App\Factory;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\RecurringInvoice;

/**
 * Class RecurringInvoiceToInvoiceFactory
 * @package App\Factory
 */
class RecurringInvoiceToInvoiceFactory
{
    /**
     * @param RecurringInvoice $recurring_invoice
     * @param Customer $customer
     * @return Invoice
     */
    public static function create(RecurringInvoice $recurring_invoice, Customer $customer): Invoice
    {
        $invoice = new Invoice();
        $invoice->fill($recurring_invoice->toArray());
        $invoice->number = null;
        $invoice->setAccount($recurring_invoice->account);
        $invoice->setStatus(Invoice::STATUS_DRAFT);
        $invoice->setCustomer($recurring_invoice->customer);
        $invoice->setDueDate();
        $invoice->setTotal($recurring_invoice->total);
        $invoice->setBalance($recurring_invoice->total);
        $invoice->setUser($recurring_invoice->user);
        $invoice->setNumber();
        $invoice->recurring_invoice_id = $recurring_invoice->id;

        return $invoice;
    }
}
