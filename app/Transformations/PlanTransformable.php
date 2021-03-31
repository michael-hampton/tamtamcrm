<?php

namespace App\Transformations;


use App\Models\Plan;

trait PlanTransformable
{
    public function transformPlan(Plan $plan)
    {
        return [
            'id'                       => (int)$plan->id,
            'name'                     => $plan->name,
            'description'              => $plan->description,
            'code'                     => $plan->code,
            'price'                    => $plan->price,
            'interval_unit'            => $plan->interval_unit,
            'interval_count'           => $plan->interval_count,
            'trial_period'             => $plan->trial_period,
            'sort_order'               => $plan->sort_order,
            'is_active'                => $plan->is_active,
            'signup_fee'               => $plan->signup_fee,
            'currency'                 => $plan->currency,
            'invoice_period'           => $plan->invoice_period,
            'invoice_interval'         => $plan->invoice_interval,
            'grace_period'             => $plan->grace_period,
            'grace_interval'           => $plan->grace_interval,
            'prorate_period'           => $plan->prorate_period,
            'prorate_day'              => $plan->prorate_day,
            'prorate_extend_due'       => $plan->prorate_extend_due,
            'active_subscribers_limit' => $plan->active_subscribers_limit,
            'trial_interval'           => $plan->trial_interval,
            'user_id'                  => (int)$plan->user_id,
            'account_id'               => (int)$plan->account_id,
            'assigned_to'              => (int)$plan->assigned_to,
            'can_cancel_plan'          => (bool)$plan->can_cancel_plan,
            'auto_billing_enabled'     => (bool)$plan->auto_billing_enabled
        ];
    }
}