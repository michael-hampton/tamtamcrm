<?php

namespace App\Console\Commands;

use App\Actions\Account\ConvertAccount;
use App\Components\InvoiceCalculator\LineItem;
use App\Factory\InvoiceFactory;
use App\Jobs\ProcessSubscription;
use App\Mail\Account\SubscriptionInvoice;
use App\Models\Account;
use App\Models\Domain;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-subscription-renewals';

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
        ProcessSubscription::dispatchNow();
    }
}
