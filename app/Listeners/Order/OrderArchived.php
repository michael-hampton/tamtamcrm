<?php

namespace App\Listeners\Order;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderArchived implements ShouldQueue
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
            'id'          => $event->order->id,
            'customer_id' => $event->order->customer_id,
            'message'     => 'A order was archived'
        ];

        $fields = [
            'notifiable_id'   => $event->order->user_id,
            'account_id'      => $event->order->account_id,
            'notifiable_type' => get_class($event->order),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'archived'
        ];

        $notification = NotificationFactory::create($event->order->account_id, $event->order->user_id);
        $notification->entity_id = $event->order->id;
        $this->notification_repo->save($notification, $fields);
    }
}
