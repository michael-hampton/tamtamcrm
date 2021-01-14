<?php

namespace App\Listeners\Credit;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreditUpdated implements ShouldQueue
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
        $fields['data']['id'] = $event->credit->id;
        $fields['data']['customer_id'] = $event->credit->customer_id;
        $fields['data']['message'] = 'A credit was updated';
        $fields['notifiable_id'] = $event->credit->user_id;
        $fields['account_id'] = $event->credit->account_id;
        $fields['notifiable_type'] = get_class($event->credit);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->credit->account_id, $event->credit->user_id);
        $notification->entity_id = $event->credit->id;
        $this->notification_repo->save($notification, $fields);

        // regenerate pdf
        $event->credit->service()->generatePdf(null, true);
    }
}
