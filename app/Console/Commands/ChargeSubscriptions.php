<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Illuminate\Console\Command;

class ChargeSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge-subscriptions';

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
     */
    public function handle()
    {
        $invoices = Invoice::whereRaw('DATE(due_date) = CURDATE()')->get();

        if (empty($invoices)) {
            return false;
        }

        $invoice_repo = new InvoiceRepository(new Invoice());

        foreach ($invoices as $invoice) {
            // check account
            if (!$this->checkAccount($invoice)) {
                continue;
            }

            // check invoice
            if (!$this->checkInvoiceIsSubscription($invoice)) {
                continue;
            }

            $invoice->service()->autoBill($invoice_repo);
        }
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    private function checkAccount(Invoice $invoice)
    {
        $domain = Domain::where('customer_id', $invoice->customer_id)->where('user_id', $invoice->user_id)->first();

        if (empty($domain)) {
            return false;
        }

        return true;
    }

    private function checkInvoiceIsSubscription(Invoice $invoice)
    {
        $is_subscription = false;

        foreach ($invoice->line_items as $line_item) {
            if ($line_item->type_id === Invoice::SUBSCRIPTION_TYPE) {
                $is_subscription = true;
            }
        }

        return $is_subscription;
    }
}
