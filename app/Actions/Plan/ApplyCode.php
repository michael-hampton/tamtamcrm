<?php

namespace App\Actions\Plan;


use App\Components\Promocodes\Promocodes;
use App\Models\Account;
use App\Models\Plan;

class ApplyCode
{
    /**
     * @param Plan $plan
     * @param Account $account
     * @param float $cost
     * @return array
     * @throws \Exception
     */
    public function execute(Plan $plan, Account $account, float $cost, int $quantity)
    {
        $promocode = (new Promocodes)->checkPlan($account, $plan, $plan->domain->customer);

        if (empty($promocode)) {
            throw new \Exception('Invalid promocode');
        }

        $amount = $promocode->reward;
        $amount_type = $promocode->amount_type;

        /* if($quantity > 1) {
            $cost *= $quantity;
        } */

        $cost = $amount_type === 'pct' ? $cost * ((100 - $amount) / 100) : $cost - $amount;

        $promocode->delete();

        $plan->promocode_applied = true;
        $plan->save();

        return [
            'promocode'          => $promocode->code,
            'amount'             => $amount_type === 'pct' ? ($amount / 100) * $cost : $amount,
            'cost'               => $cost,
            'is_amount_discount' => $amount_type === 'pct'
        ];
    }
}