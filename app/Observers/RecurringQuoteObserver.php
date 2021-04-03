<?php

namespace App\Observers;

use App\Components\InvoiceCalculator\InvoiceCalculator;
use App\Models\RecurringQuote;
use App\Traits\BuildVariables;
use App\Traits\Money;

class RecurringQuoteObserver
{
    use Money;
    use BuildVariables;

    /**
     * Handle the RecurringQuote "created" event.
     *
     * @param \App\Models\RecurringQuote $recurringQuote
     * @return void
     */
    public function created(RecurringQuote $recurringQuote)
    {
        //
    }

    /**
     * Handle the RecurringQuote "updated" event.
     *
     * @param \App\Models\RecurringQuote $recurringQuote
     * @return void
     */
    public function updated(RecurringQuote $recurringQuote)
    {
        //
    }

    /**
     * Handle the RecurringQuote "deleted" event.
     *
     * @param \App\Models\RecurringQuote $recurringQuote
     * @return void
     */
    public function deleted(RecurringQuote $recurringQuote)
    {
        //
    }

    /**
     * Handle the RecurringQuote "restored" event.
     *
     * @param \App\Models\RecurringQuote $recurringQuote
     * @return void
     */
    public function restored(RecurringQuote $recurringQuote)
    {
        //
    }

    /**
     * Handle the RecurringQuote "force deleted" event.
     *
     * @param \App\Models\RecurringQuote $recurringQuote
     * @return void
     */
    public function forceDeleted(RecurringQuote $recurringQuote)
    {
        //
    }

    public function saving(RecurringQuote $recurring_quote)
    {
        if (!empty($recurring_quote->line_items)) {
            $recurring_quote = (new InvoiceCalculator($recurring_quote))->build()->rebuildEntity();
        }

        $this->convertCurrencies(
            $recurring_quote,
            $recurring_quote->total,
            config('taskmanager.use_live_exchange_rates')
        );
        $this->populateDefaults($recurring_quote);
        $this->formatNotes($recurring_quote);
    }
}
