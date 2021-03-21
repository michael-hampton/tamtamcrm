<?php

namespace App\Jobs;

use App\Actions\Plan\ApplyCode;
use App\Components\InvoiceCalculator\LineItem;
use App\Factory\InvoiceFactory;
use App\Mail\Account\SubscriptionInvoice;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\PlanSubscription;
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
        $plans = PlanSubscription::join('plans', 'plans.id', '=', 'plan_subscriptions.plan_id')
                                 ->where('due_date', '=', now()->addDays(10))
                                 ->where('plans.is_active', '=', 1)
                                 ->whereNull('cancelled_at')
                                 ->where('ends_at', '>', now())
                                 ->get();


        foreach ($plans as $plan) {
            $account = $plan->domain->default_company;

            if ($plan->onTrial() || (!empty($plan->trial_ends_at) && $plan->trial_ends_at <= now()->addDays(10)->format('Y-m-d'))) {
                continue;
            }

//                $due_date = Carbon::parse($plan->due_date)->subDays(10)->format('Y-m-d');
//                $expiry_date = $plan->ends_at;
//
//                if ($due_date === $expiry_date) {
//                    (new UpgradePlan())->execute($plan->domain);
//                }

//                continue;

            $unit_cost = $plan->plan->price;

            $promocode = [];
            $promocode_applied = false;

            if (!empty($plan->promocode) && empty($plan->promocode_applied)) {
                $promocode = (new ApplyCode())->execute($plan, $account, $unit_cost);
                $promocode_applied = true;
            }

            $due_date = Carbon::now()->addDays(10);

            $invoice = $this->createInvoice($plan, $unit_cost, $plan->number_of_licences, $due_date, $promocode);

            if (!empty($account->support_email)) {
                Mail::to($account->support_email)->send(new SubscriptionInvoice($plan, $account, $invoice));
            }

            $due_date = $plan->plan->invoice_interval === 'year' ? now()->addDays(10)->addYearNoOverflow(
            ) : now()->addDays(10)->addMonthNoOverflow();

            $plan->update(['due_date' => $due_date, 'promocode_applied' => $promocode_applied]);
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
        PlanSubscription $plan,
        float $total_to_pay,
        int $quantity,
        $due_date,
        array $promocode = []
    ): Invoice {
//        if (empty($account->domains) || empty($account->domains->user_id)) {
//            $account = (new ConvertAccount($account))->execute();
//        }

        $customer = $plan->customer;

        $user = $customer->user;

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
