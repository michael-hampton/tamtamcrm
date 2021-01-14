<?php

namespace App\Events\Quote;

use App\Models\Invitation;
use App\Models\QuoteInvitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteWasEmailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Invitation
     */
    public Invitation $invitation;

    /**
     * QuoteWasEmailed constructor.
     * @param Invitation $invitation
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }
}
