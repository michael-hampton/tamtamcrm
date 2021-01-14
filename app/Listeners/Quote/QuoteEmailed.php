<?php

namespace App\Listeners\Quote;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuoteEmailed implements ShouldQueue
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
        $fields['data']['id'] = $event->invitation->inviteable->id;
        $fields['data']['customer_id'] = $event->invitation->inviteable->customer_id;
        $fields['data']['message'] = 'An inviteable was emailed';
        $fields['data']['contact_id'] = $event->invitation->inviteable->contact_id;
        $fields['notifiable_id'] = $event->invitation->inviteable->user_id;
        $fields['account_id'] = $event->invitation->inviteable->account_id;
        $fields['notifiable_type'] = get_class($event->invitation->inviteable);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification =
            NotificationFactory::create(
                $event->invitation->inviteable->account_id,
                $event->invitation->inviteable->user_id
            );
        $notification->entity_id = $event->invitation->inviteable->id;
        $this->notification_repo->save($notification, $fields);
    }
}
