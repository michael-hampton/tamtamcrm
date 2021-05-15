<?php

namespace App\Listeners\Quote;

use App\Services\Pdf\GeneratePdf;
use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuoteUpdated implements ShouldQueue
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
            'id'          => $event->quote->id,
            'customer_id' => $event->quote->customer_id,
            'message'     => 'A quote was updated'
        ];

        $fields = [
            'notifiable_id'   => $event->quote->user_id,
            'account_id'      => $event->quote->account_id,
            'notifiable_type' => get_class($event->quote),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'updated'
        ];

        $notification = NotificationFactory::create($event->quote->account_id, $event->quote->user_id);
        $notification->entity_id = $event->quote->id;
        $this->notification_repo->save($notification, $fields);

        // regenerate pdf
        (new GeneratePdf($event->quote))->execute(null, true);
    }
}
