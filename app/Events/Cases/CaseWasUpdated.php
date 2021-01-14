<?php

namespace App\Events\Cases;

use App\Models\Cases;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class CaseWasUpdated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Cases
     */
    public Cases $case;

    /**
     * CaseWasUpdated constructor.
     * @param Cases $case
     */
    public function __construct(Cases $case)
    {
        $this->case = $case;
        $this->send($case, get_class($this));
    }
}
