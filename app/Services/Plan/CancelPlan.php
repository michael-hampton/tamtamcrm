<?php


namespace App\Services\Plan;


use App\Components\Refund\RefundFactory;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PlanSubscription;
use App\Repositories\CreditRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CancelPlan
{

    /**
     * @param PlanSubscription $plan_subscription
     * @return Payment|null
     */
    public function execute(PlanSubscription $plan_subscription): ?Payment
    {
        $invoice = Invoice::subscriptions($plan_subscription)
            ->paid()
            ->whereHas('payments')
            ->latest()
            ->first();

        $invoice_start_date = Carbon::parse($invoice->date);
        $end_date = $invoice_start_date->addDays($plan_subscription->plan->grace_period);

        if ($end_date->lt(now())) {
            return null;
        }

        $payment = $invoice->payments()->first();

        $data = [
            'invoices' => [
                0 => [
                    'invoice_id' => $invoice->id,
                    'amount' => $invoice->total
                ]
            ]
        ];

        return (new RefundFactory())->createRefund($payment, $data, new CreditRepository(new Credit()));
    }
}