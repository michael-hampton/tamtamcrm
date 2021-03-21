<?php

namespace App\Actions\Plan;


use App\Components\Promocodes\Promocodes;
use App\Models\Account;
use App\Models\Plan;
use App\Models\PlanSubscription;

class ApplyCode
{
    /**
     * @param Plan $plan
     * @param Account $account
     * @param float $cost
     * @return array
     * @throws \Exception
     */
    public function execute(PlanSubscription $plan, Account $account, float $cost)
    {
        $promocode = (new Promocodes)->checkPlan($account, $plan, $plan->domain->customer);

        if (empty($promocode)) {
            throw new \Exception('Invalid promocode');
        }

        $amount = $promocode->reward;
        $amount_type = $promocode->amount_type;

        $quantity = $plan->number_of_licences;

        if ($quantity > 1) {
            $cost *= $quantity;
        }

        $cost = $amount_type === 'pct' ? $cost * ((100 - $amount) / 100) : $cost - $amount;

        $promocode->delete();

        return [
            'promocode'          => $promocode->code,
            'amount'             => $amount_type === 'pct' ? ($amount / 100) * $cost : $amount,
            'cost'               => $cost,
            'is_amount_discount' => $amount_type === 'pct'
        ];
    }
}