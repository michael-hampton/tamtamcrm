<?php

namespace App\Listeners\RecurringQuote;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecurringQuoteCreated implements ShouldQueue
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
        $fields['data']['id'] = $event->recurring_quote->id;
        $fields['data']['customer_id'] = $event->recurring_quote->customer_id;
        $fields['data']['message'] = 'A recurring_quote was created';
        $fields['notifiable_id'] = $event->recurring_quote->user_id;
        $fields['account_id'] = $event->recurring_quote->account_id;
        $fields['notifiable_type'] = get_class($event->recurring_quote);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create(
            $event->recurring_quote->account_id,
            $event->recurring_quote->user_id
        );
        $notification->entity_id = $event->recurring_quote->id;
        $this->notification_repo->save($notification, $fields);
    }
}
