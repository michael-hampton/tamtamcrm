<?php

namespace App\Observers;

use App\Actions\Transaction\TriggerTransaction;
use App\Components\InvoiceCalculator\InvoiceCalculator;
use App\Models\Credit;
use App\Models\Invoice;
use App\Traits\BuildVariables;
use App\Traits\Money;

class CreditObserver
{
    use BuildVariables;
    use Money;

    /**
     * Handle the Credit "created" event.
     *
     * @param \App\Models\Credit $credit
     * @return void
     */
    public function created(Credit $credit)
    {
        //
    }

    /**
     * Handle the Credit "updated" event.
     *
     * @param \App\Models\Credit $credit
     * @return void
     */
    public function updated(Credit $credit)
    {
        //
    }

    /**
     * Handle the Credit "deleted" event.
     *
     * @param \App\Models\Credit $credit
     * @return void
     */
    public function deleted(Credit $credit)
    {
        //
    }

    /**
     * Handle the Credit "restored" event.
     *
     * @param \App\Models\Credit $credit
     * @return void
     */
    public function restored(Credit $credit)
    {
        //
    }

    /**
     * Handle the Credit "force deleted" event.
     *
     * @param \App\Models\Credit $credit
     * @return void
     */
    public function forceDeleted(Credit $credit)
    {
        //
    }

    public function saving(Credit $credit)
    {

    }

    public function saved(Credit $credit)
    {
        $invoice_total = $credit->total * 1;
        $original_amount = $credit->getOriginal('total') * 1;

        if ($credit->status_id !== Credit::STATUS_DRAFT && $original_amount !== $invoice_total) {
            $updated_amount = $credit->total - $original_amount;
            (new TriggerTransaction($credit))->execute($updated_amount, $credit->customer->balance);
        }
    }
}
