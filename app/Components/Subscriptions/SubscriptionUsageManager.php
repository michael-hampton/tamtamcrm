<?php

namespace App\Components\Subscriptions;


use App\Models\Feature;
use App\Models\PlanSubscription;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class SubscriptionUsageManager
{
    /**
     * @var PlanSubscription
     */
    protected PlanSubscription $subscription;

    /**
     * SubscriptionUsageManager constructor.
     * @param PlanSubscription $subscription
     */
    public function __construct(PlanSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @param $featureId
     * @param int $uses
     * @return Model
     * @throws Throwable
     */
    public function reduce($featureId, $uses = 1)
    {
        return $this->record($featureId, -$uses);
    }

    /**
     * @param string $featureCode
     * @param int $uses
     * @param bool $incremental
     * @return Model
     * @throws Throwable
     */
    public function record(string $featureCode, $uses = 1, $incremental = true)
    {
        $feature = Feature::code($featureCode)->first();

        $usage = $this->subscription->usage()->firstOrNew(
            [
                'feature_code' => $feature->code,
            ]
        );

        if ($feature->isResettable()) {
            // Set expiration date when the usage record is new or doesn't have one.
            if (is_null($usage->valid_until)) {
                // Set date from subscription creation date so the reset period match the period specified
                // by the subscription's plan.
                $usage->valid_until = $feature->getResetTime($this->subscription->created_at);
            } elseif ($usage->isExpired()) {
                // If the usage record has been expired, let's assign
                // a new expiration date and reset the uses to zero.
                $usage->valid_until = $feature->getResetTime($usage->valid_until);
                $usage->used = 0;
            }
        }

        $usage->used = max($incremental ? $usage->used + $uses : $uses, 0);

        $usage->saveOrFail();

        return $usage;
    }

    /**
     * Clear usage data.
     *
     * @return self
     */
    public function clear()
    {
        $this->subscription->usage()->delete();

        return $this;
    }
}