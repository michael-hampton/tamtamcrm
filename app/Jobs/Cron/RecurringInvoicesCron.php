<?php

namespace App\Jobs\Cron;

use App\Jobs\RecurringInvoice\SendRecurring;
use App\Models\RecurringInvoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RecurringInvoicesCron
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $recurring_invoices = RecurringInvoice::where('next_send_date', '<=', Carbon::now()->addMinutes(30))->get();
        Log::info(
            Carbon::now()->addMinutes(30) . ' Sending Recurring Invoices. Count = ' .
            $recurring_invoices->count()
        );
        $recurring_invoices->each(
            function ($recurring_invoice, $key) {
                dispatch(new SendRecurring($recurring_invoice));
            }
        );
    }


}
