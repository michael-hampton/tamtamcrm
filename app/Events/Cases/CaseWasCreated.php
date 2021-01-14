<?php

namespace App\Events\Cases;

use App\Models\Cases;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class CaseWasCreated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Cases
     */
    public Cases $case;

    /**
     * Create a new event instance.
     *
     * @param Cases $case $case
     */
    public function __construct(Cases $case)
    {
        $this->case = $case;
        $this->send($case, get_class($this));
    }
}
