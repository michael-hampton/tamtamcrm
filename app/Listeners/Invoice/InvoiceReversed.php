<?php

namespace App\Listeners\Invoice;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceReversed implements ShouldQueue
{
    protected $activity_repo;


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
        $fields['data']['id'] = $event->invoice->id;
        $fields['data']['customer_id'] = $event->invoice->customer_id;
        $fields['data']['message'] = 'An invoice payment was reversed';
        $fields['notifiable_id'] = $event->invoice->user_id;
        $fields['account_id'] = $event->invoice->account_id;
        $fields['notifiable_type'] = get_class($event->invoice);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->invoice->account_id, $event->invoice->user_id);
        $notification->entity_id = $event->invoice->id;
        $this->notification_repo->save($notification, $fields);
    }
}
