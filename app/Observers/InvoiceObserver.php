<?php

namespace App\Observers;

use App\Actions\Invoice\AttachEntities;
use App\Actions\Invoice\CloneExpenseDocuments;
use App\Models\Invoice;
use App\Traits\BuildVariables;

class InvoiceObserver
{
    use BuildVariables;

    /**
     * Handle the Invoice "created" event.
     *
     * @param \App\Models\Invoice $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "updated" event.
     *
     * @param \App\Models\Invoice $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "deleted" event.
     *
     * @param \App\Models\Invoice $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     *
     * @param \App\Models\Invoice $invoice
     * @return void
     */
    public function restored(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     *
     * @param \App\Models\Invoice $invoice
     * @return void
     */
    public function forceDeleted(Invoice $invoice)
    {
        //
    }

    public function saving(Invoice $invoice)
    {
    }

    public function creating(Invoice $invoice)
    {
    }

    public function saved(Invoice $invoice)
    {
        $entities_added = (new AttachEntities($invoice))->attach();

        if (!empty($entities_added['expenses'])) {
            (new CloneExpenseDocuments($invoice))->clone($entities_added['expenses']);
        }
    }
}
