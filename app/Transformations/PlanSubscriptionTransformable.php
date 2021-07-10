<?php

namespace App\Transformations;


use App\Models\PlanSubscription;

trait PlanSubscriptionTransformable
{
    public function transformPlanSubscription(PlanSubscription $plan_subscription)
    {
        return [
            'id'                    => (int)$plan_subscription->id,
            'plan_id'               => (int)$plan_subscription->plan_id,
            'name'                  => $plan_subscription->name,
            'plan_name'             => $plan_subscription->plan_name,
            'starts_at'             => $plan_subscription->starts_at,
            'ends_at'               => $plan_subscription->ends_at,
            'trial_ends_at'         => $plan_subscription->trial_ends_at,
            'cancelled_at'          => $plan_subscription->cancelled_at,
            'cancelled_immediately' => $plan_subscription->cancelled_immediately,
            'subscriber_type'       => $plan_subscription->subscriber_type,
            'subscriber_id'         => $plan_subscription->subscriber_id,
            'domain_id'             => $plan_subscription->domain_id,
            'account_id'            => $plan_subscription->account_id,
            'due_date'              => $plan_subscription->due_date,
            'number_of_licences'    => $plan_subscription->number_of_licences,
            'promocode'             => $plan_subscription->promocode
        ];
    }
}