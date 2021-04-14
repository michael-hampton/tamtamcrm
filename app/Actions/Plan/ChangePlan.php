<?php


namespace App\Actions\Plan;


use App\Models\Invoice;
use App\Models\PlanSubscription;
use phpDocumentor\Reflection\Types\Integer;

class ChangePlan
{

    public function execute(PlanSubscription $plan_subscription)
    {
        $invoice = Invoice::where('plan_subscription_id', '=', $plan_subscription->id)->latest()->first();

        $amount = $invoice->balance > 0 ? $plan_subscription->calculateRefundAmount($invoice, true) : $plan_subscription->calculateRefundAmount($invoice, false) * -1;

        $amount = $amount + $plan_subscription->plan->price;

        if($amount > 0) {
            $plan_subscription->amount_owing = $amount;
            $plan_subscription->save();
        }

        return true;
    }
}