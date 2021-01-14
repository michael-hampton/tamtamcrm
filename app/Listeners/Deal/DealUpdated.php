<?php

namespace App\Listeners\Deal;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class DealUpdated implements ShouldQueue
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
        $fields['data']['id'] = $event->deal->id;
        $fields['data']['customer_id'] = $event->deal->customer_id;
        $fields['data']['message'] = 'A deal was updated';
        $fields['notifiable_id'] = $event->deal->user_id;
        $fields['account_id'] = $event->deal->account_id;
        $fields['notifiable_type'] = get_class($event->deal);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->deal->account_id, $event->deal->user_id);
        $notification->entity_id = $event->deal->id;
        $this->notification_repo->save($notification, $fields);

        // regenerate pdf
        $event->deal->service()->generatePdf(null, true);
    }
}
