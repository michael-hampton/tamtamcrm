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
        $fields = [];
        $fields['data']['id'] = $event->purchase_order->id;
        $fields['data']['company_id'] = $event->purchase_order->company_id;
        $fields['data']['message'] = 'A purchase order was deleted';
        $fields['notifiable_id'] = $event->purchase_order->user_id;
        $fields['account_id'] = $event->purchase_order->account_id;
        $fields['notifiable_type'] = get_class($event->purchase_order);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create(
            $event->purchase_order->account_id,
            $event->purchase_order->user_id
        );
        $notification->entity_id = $event->purchase_order->id;
        $this->notification_repo->save($notification, $fields);
    }
}
