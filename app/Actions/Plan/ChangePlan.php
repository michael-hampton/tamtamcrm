<?php


namespace App\Actions\Plan;


use App\Models\Invoice;
use App\Models\PlanSubscription;
use phpDocumentor\Reflection\Types\Integer;

class ChangePlan
{

    public function execute(PlanSubscription $plan_subscription)
    {
        $invoice = Invoice::subscriptions($plan_subscription)->latest()->first();

        if (empty($invoice)) {
            return true;
        }

        $amount = $plan_subscription->calculateRefundAmount($invoice);

        $amount = $amount + $plan_subscription->plan->price;

        if ($amount > 0) {
            $plan_subscription->amount_owing = $amount;
            $plan_subscription->save();
        }

        return true;
    }
}