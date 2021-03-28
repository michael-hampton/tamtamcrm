<?php

namespace App\Listeners\Credit;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreditDeleted implements ShouldQueue
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
            'id'          => $event->credit->id,
            'customer_id' => $event->credit->customer_id,
            'message'     => 'A credit was deleted'
        ];

        $fields = [
            'notifiable_id'   => $event->credit->user_id,
            'account_id'      => $event->credit->account_id,
            'notifiable_type' => get_class($event->credit),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'deleted'
        ];

        $notification = NotificationFactory::create($event->credit->account_id, $event->credit->user_id);
        $notification->entity_id = $event->credit->id;

        $this->notification_repo->save($notification, $fields);
    }
}
