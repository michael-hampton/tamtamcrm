<?php

namespace App\Listeners\Customer;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerCreated implements ShouldQueue
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
        $data = [
            'id'      => $event->customer->id,
            'message' => 'A customer was created'
        ];

        $fields = [
            'notifiable_id'   => $event->customer->user_id,
            'account_id'      => $event->customer->account_id,
            'notifiable_type' => get_class($event->customer),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'created'
        ];

        $notification = NotificationFactory::create($event->customer->account_id, $event->customer->user_id);
        $notification->entity_id = $event->customer->id;
        $this->notification_repo->save($notification, $fields);
    }
}
