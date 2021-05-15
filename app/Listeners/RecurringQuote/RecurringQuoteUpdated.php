<?php

namespace App\Listeners\RecurringQuote;

use App\Services\Pdf\GeneratePdf;
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
        $data = [
            'id'          => $event->recurring_quote->id,
            'customer_id' => $event->recurring_quote->customer_id,
            'message'     => 'A recurring quote was updated'
        ];

        $fields = [
            'notifiable_id'   => $event->recurring_quote->user_id,
            'account_id'      => $event->recurring_quote->account_id,
            'notifiable_type' => get_class($event->recurring_quote),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'updated'
        ];

        $notification = NotificationFactory::create(
            $event->recurring_quote->account_id,
            $event->recurring_quote->user_id
        );
        $notification->entity_id = $event->recurring_quote->id;
        $this->notification_repo->save($notification, $fields);

        // regenerate pdf
        (new GeneratePdf($event->recurring_quote))->execute(null, true);
    }
}
