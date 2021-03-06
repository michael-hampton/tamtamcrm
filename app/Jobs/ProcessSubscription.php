<?php

namespace App\Jobs;

use App\Actions\Account\ConvertAccount;
use App\Components\InvoiceCalculator\LineItem;
use App\Factory\InvoiceFactory;
use App\Mail\Account\SubscriptionInvoice;
use App\Models\Account;
use App\Models\Domain;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
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
            $account = $domain->default_company;

            $cost = $domain->subscription_period === Domain::SUBSCRIPTION_PERIOD_YEAR ? env(
                'YEARLY_ACCOUNT_PRICE'
            ) : env('MONTHLY_ACCOUNT_PRICE');

            $number_of_licences = $domain->allowed_number_of_users;

            if ($number_of_licences > 1 && $number_of_licences !== 99999) {
                $cost *= $number_of_licences;
            }

            $due_date = Carbon::now()->addDays(10);

            $invoice = $this->createInvoice($account, $cost, $due_date);

            Mail::to($account->support_email)->send(new SubscriptionInvoice($account, $invoice));

            $domain->subscription_expiry_date = $domain->subscription_period === Domain::SUBSCRIPTION_PERIOD_YEAR ? now(
            )->addDays(10)->addYearNoOverflow() : now()->addDays(10)->addMonthNoOverflow();
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
            $account = (new ConvertAccount($account))->execute();
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

        $invoice_repo = new InvoiceRepository(new Invoice);
        $invoice = $invoice_repo->save(['line_items' => $line_items], $invoice);
        $invoice_repo->markSent($invoice);

        return $invoice;
    }
}
