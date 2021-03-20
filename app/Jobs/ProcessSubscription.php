<?php

namespace App\Jobs;

use App\Actions\Plan\ApplyCode;
use App\Actions\Plan\UpgradePlan;
use App\Components\InvoiceCalculator\LineItem;
use App\Components\Promocodes\Promocodes;
use App\Factory\InvoiceFactory;
use App\Mail\Account\SubscriptionInvoice;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Plan;
use App\Repositories\InvoiceRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
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
        $plans = Plan::whereRaw('DATEDIFF(due_date, CURRENT_DATE) = 10')
                     ->where('is_active', 1)
            //->where('due_date', '<=', 'expiry_date')
                     ->whereIn(
                'plan',
                array(Plan::PLAN_TRIAL, Plan::PLAN_STANDARD, Plan::PLAN_ADVANCED)
            )
                     ->get();

        foreach ($plans as $plan) {
            $account = $plan->domain->default_company;

            if ($plan->plan === Plan::PLAN_TRIAL) {
                $due_date = Carbon::parse($plan->due_date)->subDays(10)->format('Y-m-d');
                $expiry_date = $plan->expiry_date;

                if ($due_date === $expiry_date) {
                    (new UpgradePlan())->execute($plan->domain);
                }

                continue;
            }

            $unit_cost = $plan->calculateCost();

            $promocode = [];

            if (!empty($plan->promocode) && empty($plan->promocode_applied)) {
                $promocode = (new ApplyCode())->execute($plan, $account, $unit_cost);
            }

            $due_date = Carbon::now()->addDays(10);

            $invoice = $this->createInvoice($plan, $unit_cost, $plan->number_of_licences, $due_date, $promocode);

            if (!empty($account->support_email)) {
                Mail::to($account->support_email)->send(new SubscriptionInvoice($plan, $account, $invoice));
            }

            $plan->expiry_date = $plan->plan_period === Plan::PLAN_PERIOD_YEAR ? now()->addDays(10)->addYearNoOverflow(
            ) : now()->addDays(10)->addMonthNoOverflow();
        }
    }

    /**
     * @param Plan $plan
     * @param float $total_to_pay
     * @param $due_date
     * @param array|bool $promocode
     * @return Invoice
     * @throws \ReflectionException
     */
    private function createInvoice(
        Plan $plan,
        float $total_to_pay,
        int $quantity,
        $due_date,
        array $promocode = []
    ): Invoice {
//        if (empty($account->domains) || empty($account->domains->user_id)) {
//            $account = (new ConvertAccount($account))->execute();
//        }

        $customer = $plan->customer;

        $user = $plan->user;

        $invoice = InvoiceFactory::create($plan->domain->default_company, $user, $customer);
        $invoice->due_date = $due_date;

        $line_items[] = (new LineItem)
            ->setProductId($plan->id)
            ->setQuantity($quantity)
            ->setUnitPrice($total_to_pay)
            ->setTypeId(Invoice::SUBSCRIPTION_TYPE)
            ->setNotes("Plan charge for {$plan->domain->default_company->subdomain}")
            ->toObject();

        $data = ['line_items' => $line_items];

        if (!empty($promocode)) {
            $data['discount_total'] = $promocode['amount'];
            $data['voucher_code'] = $promocode['promocode'];
            $data['is_amount_discount'] = $promocode['is_amount_discount'];
        }

        $invoice_repo = new InvoiceRepository(new Invoice);
        $invoice = $invoice_repo->create($data, $invoice);
        $invoice_repo->markSent($invoice);

        return $invoice;
    }
}
