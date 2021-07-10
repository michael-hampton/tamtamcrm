<?php

namespace App\Observers;

use App\Components\InvoiceCalculator\InvoiceCalculator;
use App\Models\RecurringInvoice;
use App\Traits\BuildVariables;
use App\Traits\Money;

class RecurringInvoiceObserver
{
    use Money;
    use BuildVariables;

    /**
     * Handle the RecurringInvoice "created" event.
     *
     * @param \App\Models\RecurringInvoice $recurringInvoice
     * @return void
     */
    public function created(RecurringInvoice $recurringInvoice)
    {
        //
    }

    /**
     * Handle the RecurringInvoice "updated" event.
     *
     * @param \App\Models\RecurringInvoice $recurringInvoice
     * @return void
     */
    public function updated(RecurringInvoice $recurringInvoice)
    {
        //
    }

    /**
     * Handle the RecurringInvoice "deleted" event.
     *
     * @param \App\Models\RecurringInvoice $recurringInvoice
     * @return void
     */
    public function deleted(RecurringInvoice $recurringInvoice)
    {
        //
    }

    /**
     * Handle the RecurringInvoice "restored" event.
     *
     * @param \App\Models\RecurringInvoice $recurringInvoice
     * @return void
     */
    public function restored(RecurringInvoice $recurringInvoice)
    {
        //
    }

    /**
     * Handle the RecurringInvoice "force deleted" event.
     *
     * @param \App\Models\RecurringInvoice $recurringInvoice
     * @return void
     */
    public function forceDeleted(RecurringInvoice $recurringInvoice)
    {
        //
    }

    public function saving(RecurringInvoice $recurring_invoice)
    {
        if (!empty($recurring_invoice->line_items)) {
            $recurring_invoice = (new InvoiceCalculator($recurring_invoice))->build()->rebuildEntity();
        }

        $this->convertCurrencies(
            $recurring_invoice,
            $recurring_invoice->total,
            config('taskmanager.use_live_exchange_rates')
        );
        $this->populateDefaults($recurring_invoice);
        $this->formatNotes($recurring_invoice);
    }
}
