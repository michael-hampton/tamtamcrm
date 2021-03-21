<?php

namespace App\Events\Plan;


use App\Models\PlanSubscription;

class SubscriptionPlanChanged
{
    /**
     * @var PlanSubscription
     */
    protected $subscription;

    /**
     * SubscriptionPlanChanged constructor.
     * @param PlanSubscription $subscription
     */
    public function __construct(PlanSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return PlanSubscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }
}