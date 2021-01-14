<?php

namespace App\Listeners\Order;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderHeld implements ShouldQueue
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
        $fields['data']['id'] = $event->order->id;
        $fields['data']['customer_id'] = $event->order->customer_id;
        $fields['data']['message'] = 'A order was held';
        $fields['notifiable_id'] = $event->order->user_id;
        $fields['account_id'] = $event->order->account_id;
        $fields['notifiable_type'] = get_class($event->order);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->order->account_id, $event->order->user_id);
        $notification->entity_id = $event->order->id;
        $this->notification_repo->save($notification, $fields);
    }
}
