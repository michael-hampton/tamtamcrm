<?php

namespace App\Observers;

use App\Components\InvoiceCalculator\InvoiceCalculator;
use App\Jobs\Product\UpdateProductPrices;
use App\Models\Quote;
use App\Traits\BuildVariables;
use App\Traits\Money;

class QuoteObserver
{
    use Money;
    use BuildVariables;

    /**
     * Handle the Quote "created" event.
     *
     * @param \App\Models\Quote $quote
     * @return void
     */
    public function created(Quote $quote)
    {
        //
    }

    /**
     * Handle the Quote "updated" event.
     *
     * @param \App\Models\Quote $quote
     * @return void
     */
    public function updated(Quote $quote)
    {
        //
    }

    /**
     * Handle the Quote "deleted" event.
     *
     * @param \App\Models\Quote $quote
     * @return void
     */
    public function deleted(Quote $quote)
    {
        //
    }

    /**
     * Handle the Quote "restored" event.
     *
     * @param \App\Models\Quote $quote
     * @return void
     */
    public function restored(Quote $quote)
    {
        //
    }

    /**
     * Handle the Quote "force deleted" event.
     *
     * @param \App\Models\Quote $quote
     * @return void
     */
    public function forceDeleted(Quote $quote)
    {
        //
    }

    public function saving(Quote $quote)
    {
        if (!empty($quote->line_items)) {
            $quote = (new InvoiceCalculator($quote))->build()->rebuildEntity();
        }

        $this->convertCurrencies($quote, $quote->total, config('taskmanager.use_live_exchange_rates'));
        $this->populateDefaults($quote);
        $this->formatNotes($quote);
    }

    public function saved(Quote $quote)
    {
        if ($quote->customer->getSetting('should_update_products') === true) {
            UpdateProductPrices::dispatchNow($quote->line_items);
        }
    }
}
