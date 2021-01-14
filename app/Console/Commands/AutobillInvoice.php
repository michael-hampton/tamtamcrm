<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Illuminate\Console\Command;
use ReflectionException;

class AutobillInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autobill-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function handle()
    {
        $invoice_repo = new InvoiceRepository(new Invoice());
        $invoices = $invoice_repo->getInvoicesForAutoBilling();

        if (empty($invoices)) {
            return false;
        }

        foreach ($invoices as $invoice) {
            if ($invoice->status_id === Invoice::STATUS_DRAFT) {
                $invoice_repo->markSent($invoice);
            }

            \App\Jobs\Invoice\AutobillInvoice::dispatchNow($invoice, $invoice_repo);
        }
    }
}
