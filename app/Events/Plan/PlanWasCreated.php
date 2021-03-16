<?php

namespace App\Events\Plan;

use App\Models\Plan;
use App\Traits\SendSubscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanWasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels, SendSubscription;

    public $plan;

    /**
     * PlanWasCreated constructor.
     * @param Plan $plan
     */
    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
    }
}
