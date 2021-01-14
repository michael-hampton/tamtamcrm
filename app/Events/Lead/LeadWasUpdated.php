<?php

namespace App\Events\Lead;

use App\Models\Lead;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class LeadWasUpdated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Lead
     */
    public Lead $lead;

    /**
     * Create a new event instance.
     *
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->send($lead, get_class($this));
    }
}
