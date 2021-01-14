<?php

namespace App\Events\CompanyToken;

use App\Models\CompanyToken;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyTokenWasDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Company
     */
    public $company_token;

    /**
     * Create a new event instance.
     *
     * @param CompanyToken $company_token
     */
    public function __construct(CompanyToken $company_token)
    {
        $this->company_token = $company_token;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
