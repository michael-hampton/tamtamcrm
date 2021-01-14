<?php

namespace App\Events\Credit;

use App\Models\Credit;
use App\Traits\SendSubscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreditWasUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels, SendSubscription;

    /**
     * @var Credit
     */
    public Credit $credit;

    /**
     * CreditWasUpdated constructor.
     * @param Credit $credit
     */
    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
        $this->send($credit, get_class($this));
    }
}
