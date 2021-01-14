<?php

namespace App\Listeners\Customer;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerUpdated implements ShouldQueue
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
        $fields['data']['id'] = $event->customer->id;
        $fields['data']['message'] = 'A customer was updated';
        $fields['notifiable_id'] = $event->customer->user_id;
        $fields['account_id'] = $event->customer->account_id;
        $fields['notifiable_type'] = get_class($event->customer);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->customer->account_id, $event->customer->user_id);
        $notification->entity_id = $event->customer->id;
        $this->notification_repo->save($notification, $fields);
    }
}
