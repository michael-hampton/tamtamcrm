<?php

namespace App\Events\Credit;

use App\Models\Credit;
use App\Traits\SendSubscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreditWasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels, SendSubscription;

    public $credit;

    /**
     * Create a new event instance.
     *
     * @param Credit $credit
     */
    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
        $this->send($credit, get_class($this));
    }
}
