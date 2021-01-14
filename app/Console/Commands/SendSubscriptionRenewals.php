<?php

namespace App\Console\Commands;

use App\Components\InvoiceCalculator\LineItem;
use App\Factory\InvoiceFactory;
use App\Mail\Account\SubscriptionInvoice;
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
        // send 10 days before
        $domains = Domain::whereRaw('DATEDIFF(subscription_expiry_date, CURRENT_DATE) = 10')
                         ->whereIn(
                             'subscription_plan',
                             array(Domain::SUBSCRIPTION_STANDARD, Domain::SUBSCRIPTION_ADVANCED)
                         )
                         ->get();

        foreach ($domains as $domain) {
            $cost = $domain->subscription_period === Domain::SUBSCRIPTION_PERIOD_YEAR ? env(
                'YEARLY_ACCOUNT_PRICE'
            ) : env('MONTHLY_ACCOUNT_PRICE');
            $due_date = Carbon::now()->addDays(10);

            $invoice = $this->createInvoice($account, $cost, $due_date);

            Mail::to($domain->support_email)->send(new SubscriptionInvoice($account, $invoice));
        }
    }

    /**
     * @param Account $account
     * @param float $total_to_pay
     * @param $due_date
     * @return Invoice
     */
    private function createInvoice(Account $account, float $total_to_pay, $due_date): Invoice
    {
        if (empty($account->domains) || empty($account->domains->user_id)) {
            $account = $account->service()->convertAccount();
        }

        $customer = $account->domains->customer;
        $user = $account->domains->user;

        $invoice = InvoiceFactory::create($account, $user, $customer);
        $invoice->due_date = $due_date;

        $line_items[] = (new LineItem)
            ->setQuantity(1)
            ->setUnitPrice($total_to_pay)
            ->setTypeId(Invoice::SUBSCRIPTION_TYPE)
            ->setNotes("Subscription charge for {$account->subdomain}")
            ->toObject();

        $invoice = (new InvoiceRepository(new Invoice))->save(['line_items' => $line_items], $invoice);

        return $invoice;
    }
}
