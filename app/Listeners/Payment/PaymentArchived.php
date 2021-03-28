<?php

namespace App\Listeners\Payment;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentArchived implements ShouldQueue
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
            'id'          => $event->payment->id,
            'customer_id' => $event->payment->customer_id,
            'message'     => 'A payment was archived'
        ];

        $fields = [
            'notifiable_id'   => $event->payment->user_id,
            'account_id'      => $event->payment->account_id,
            'notifiable_type' => get_class($event->payment),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'archived'
        ];

        $notification = NotificationFactory::create($event->payment->account_id, $event->payment->user_id);
        $notification->entity_id = $event->payment->id;
        $this->notification_repo->save($notification, $fields);
    }
}
