<?php

namespace App\Listeners\PurchaseOrder;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseOrderDeleted implements ShouldQueue
{
    /**
     * @var NotificationRepository
     */
    protected NotificationRepository $notification_repo;

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
            'id'         => $event->purchase_order->id,
            'company_id' => $event->purchase_order->company_id,
            'message'    => 'A purchase order was deleted'
        ];

        $fields = [
            'notifiable_id'   => $event->purchase_order->user_id,
            'account_id'      => $event->purchase_order->account_id,
            'notifiable_type' => get_class($event->purchase_order),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'deleted'
        ];

        $notification = NotificationFactory::create(
            $event->purchase_order->account_id,
            $event->purchase_order->user_id
        );
        $notification->entity_id = $event->purchase_order->id;
        $this->notification_repo->save($notification, $fields);
    }
}
