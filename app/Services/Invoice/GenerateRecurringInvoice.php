<?php

namespace App\Services\Invoice;


use App\Factory\InvoiceToRecurringInvoiceFactory;
use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Repositories\RecurringInvoiceRepository;

class GenerateRecurringInvoice
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function execute(array $recurring): ?RecurringInvoice
    {
        if (empty($recurring)) {
            return null;
        }

        $arrRecurring['start_date'] = $recurring['start_date'];
        $arrRecurring['expiry_date'] = $recurring['expiry_date'];
        $arrRecurring['frequency'] = $recurring['frequency'];
        $arrRecurring['grace_period'] = $recurring['grace_period'] ?: 0;
        $arrRecurring['due_date'] = $recurring['due_date'];
        $recurringInvoice = (new RecurringInvoiceRepository(new RecurringInvoice))->save(
            $arrRecurring,
            InvoiceToRecurringInvoiceFactory::create($this->invoice)
        );

        $this->invoice->recurring_invoice_id = $recurringInvoice->id;
        $this->invoice->save();

        return $recurringInvoice;
    }
}
