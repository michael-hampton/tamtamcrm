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
            $fields['data']['id'] = auth()->user()->id;
            $fields['notifiable_id'] = auth()->user()->id;
            $user = auth()->user();
        } else {
            $fields['data']['id'] = $event->user->id;
            $fields['notifiable_id'] = $event->user->id;
            $user = $event->user;
        }

        $account_id = !empty($user->account_user()) ? $user->account_user()->account_id : $user->accounts->first()->id;

        $fields['data']['message'] = 'A user was updated';
        $fields['account_id'] = $account_id;
        $fields['notifiable_type'] = get_class($event->user);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($account_id, $event->user->id);
        $notification->entity_id = $event->user->id;
        $this->notification_repo->save($notification, $fields);
    }
}
