<?php

namespace App\Events;

use App\Models\Invoice;
use App\Models\InvoiceInvitation;
use Illuminate\Queue\SerializesModels;

class QuoteInvitationWasApproved
{
    use SerializesModels;

    public $quote;
    /**
     * @var Invitation
     */
    public $invitation;

    /**
     * Create a new event instance.
     * QuoteInvitationWasApproved constructor.
     * @param Invoice $quote
     * @param InvoiceInvitation|null $invitation
     */
    public function __construct(Invoice $quote, InvoiceInvitation $invitation = null)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
