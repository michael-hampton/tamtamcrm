<?php

namespace App\Events\Order;

use App\Models\Invitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderWasEmailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Invitation
     */
    public Invitation $invitation;

    public string $template;

    /**
     * OrderWasEmailed constructor.
     * @param Invitation $invitation
     * @param string $template
     */
    public function __construct(Invitation $invitation, string $template = '')
    {
        $this->invitation = $invitation;
        $this->template = $template;
    }
}
