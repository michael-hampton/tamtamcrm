<?php

namespace App\Listeners\RecurringQuote;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecurringQuoteUpdated implements ShouldQueue
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
        $fields['data']['id'] = $event->recurringQuote->id;
        $fields['data']['customer_id'] = $event->recurringQuote->customer_id;
        $fields['data']['message'] = 'A recurringQuote was updated';
        $fields['notifiable_id'] = $event->recurringQuote->user_id;
        $fields['account_id'] = $event->recurringQuote->account_id;
        $fields['notifiable_type'] = get_class($event->recurringQuote);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create(
            $event->recurringQuote->account_id,
            $event->recurringQuote->user_id
        );
        $notification->entity_id = $event->recurringQuote->id;
        $this->notification_repo->save($notification, $fields);

        // regenerate pdf
        $event->recurringQuote->service()->generatePdf(null, true);
    }
}
