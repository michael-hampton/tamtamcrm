<?php

namespace App\Observers;

use App\Jobs\Expense\GenerateInvoice;
use App\Models\Expense;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function created(Expense $expense)
    {
        if ($expense->create_invoice === true && $expense->customer->getSetting(
                'expense_auto_create_invoice'
            ) === true) {
            GenerateInvoice::dispatchNow(new InvoiceRepository(new Invoice), collect([$expense]), $expense->toArray());
        }
    }

    /**
     * Handle the Expense "updated" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function updated(Expense $expense)
    {
        //
    }

    /**
     * Handle the Expense "deleted" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function deleted(Expense $expense)
    {
        //
    }

    /**
     * Handle the Expense "restored" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function restored(Expense $expense)
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function forceDeleted(Expense $expense)
    {
        //
    }
}
