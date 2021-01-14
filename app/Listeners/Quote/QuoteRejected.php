<?php

namespace App\Listeners\Quote;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuoteRejected implements ShouldQueue
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
        $fields['data']['id'] = $event->quote->id;
        $fields['data']['customer_id'] = $event->quote->customer_id;
        $fields['data']['message'] = 'A quote was rejected';
        $fields['notifiable_id'] = $event->quote->user_id;
        $fields['account_id'] = $event->quote->account_id;
        $fields['notifiable_type'] = get_class($event->quote);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->quote->account_id, $event->quote->user_id);
        $notification->entity_id = $event->quote->id;
        $this->notification_repo->save($notification, $fields);
    }
}
