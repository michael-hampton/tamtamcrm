<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserEmailChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public User $original_user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user, User $original_user)
    {
        $this->user = $user;
        $this->original_user = $original_user;
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
