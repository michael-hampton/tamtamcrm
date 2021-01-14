<?php

namespace App\Events\Credit;

use App\Models\Credit;
use Illuminate\Queue\SerializesModels;

class CreditWasRestored
{
    use SerializesModels;

    public Credit $credit;

    /**
     * Create a new event instance.
     *
     * @param $credit
     */
    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
    }
}
