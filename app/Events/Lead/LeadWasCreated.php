<?php

namespace App\Events\Lead;

use App\Models\Lead;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class LeadWasCreated
 * @package App\Events\Lead
 */
class LeadWasCreated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Lead $payment
     */
    public Lead $lead;

    /**
     * LeadWasCreated constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->send($lead, get_class($lead));
    }
}
