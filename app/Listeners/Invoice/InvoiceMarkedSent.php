<?php

namespace App\Listeners\Invoice;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceMarkedSent implements ShouldQueue
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
            'id'          => $event->invoice->id,
            'customer_id' => $event->invoice->customer_id,
            'message'     => 'A invoice was marked sent',
            'status'      => 'sent'
        ];

        $fields = [
            'notifiable_id'   => $event->invoice->user_id,
            'account_id'      => $event->invoice->account_id,
            'notifiable_type' => get_class($event->invoice),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'status_updated'
        ];

        $notification = NotificationFactory::create($event->invoice->account_id, $event->invoice->user_id);
        $notification->entity_id = $event->invoice->id;
        $this->notification_repo->save($notification, $fields);
        //$this->notification_repo->create($fields);
    }
}
