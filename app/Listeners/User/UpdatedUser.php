<?php

namespace App\Listeners\User;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;

class UpdatedUser
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

        if (auth()->user()) {
            $user = auth()->user();
        } else {
            $user = $event->user;
        }

        $account_id = !empty($user->account_user()) ? $user->account_user()->account_id : $user->accounts->first()->id;

        $data = [
            'id'      => $user->id,
            'message' => 'A user was updated'
        ];

        $fields = [
            'notifiable_id'   => $user->id,
            'account_id'      => $account_id,
            'notifiable_type' => get_class($event->user),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'updated'
        ];

        $notification = NotificationFactory::create($account_id, $event->user->id);
        $notification->entity_id = $event->user->id;
        $this->notification_repo->save($notification, $fields);
    }
}
