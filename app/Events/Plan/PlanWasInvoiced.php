<?php

namespace App\Events\Plan;

use App\Models\PlanSubscription;
use App\Traits\SendSubscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanWasInvoiced
{
    use Dispatchable, InteractsWithSockets, SerializesModels, SendSubscription;

    public PlanSubscription $plan_subscription;

    public array $data;

    /**
     * PlanWasInvoiced constructor.
     * @param PlanSubscription $plan_subscription
     */
    public function __construct(PlanSubscription $plan_subscription, array $data)
    {
        $this->plan_subscription = $plan_subscription;
        $this->data = $data;
    }
}
