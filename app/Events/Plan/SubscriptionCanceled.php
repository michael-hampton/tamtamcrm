<?php

namespace App\Events\Plan;


use App\Models\PlanSubscription;

class SubscriptionCanceled
{
    /**
     * @var PlanSubscription
     */
    protected $subscription;

    /**
     * SubscriptionCanceled constructor.
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