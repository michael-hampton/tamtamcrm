<?php

namespace App\Listeners\User;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;

class RestoredUser
{
    protected $notification_repo;

    /**
     * Create the event listener.
     *
     * @param NotificationRepository $notification_repo
     */
    public function __construct(NotificationRepository $notification_repo)
    {
        $this->notification_repo = $notification_repo;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $fields = [];

        if (auth()->user()->id) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = $event->user->id;
        }

        $data = [
            'id'      => $user_id,
            'message' => 'A user was restored'
        ];

        $fields = [
            'notifiable_id'   => $user_id,
            'account_id'      => $event->user->account_id,
            'notifiable_type' => get_class($event->user),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'restored'
        ];

        $notification = NotificationFactory::create($event->user->account_id, $event->user->user_id);
        $notification->entity_id = $event->user->id;
        $this->notification_repo->save($notification, $fields);
    }
}
