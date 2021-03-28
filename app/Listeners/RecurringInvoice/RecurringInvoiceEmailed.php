<?php

namespace App\Listeners\RecurringInvoice;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecurringInvoiceEmailed implements ShouldQueue
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
            'id'          => $event->invitation->inviteable->id,
            'customer_id' => $event->invitation->inviteable->customer_id,
            'message'     => 'A recurring invoice was emailed'
        ];

        $fields = [
            'notifiable_id'   => $event->invitation->inviteable->user_id,
            'account_id'      => $event->invitation->inviteable->account_id,
            'notifiable_type' => get_class($event->invitation->inviteable),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'archived'
        ];

        $notification = NotificationFactory::create(
            $event->invitation->inviteable->account_id,
            $event->invitation->inviteable->user_id
        );
        $notification->entity_id = $event->invitation->inviteable->id;
        $this->notification_repo->save($notification, $fields);

        $event->invitation->inviteable->date_notification_last_sent = Carbon::now();
        $event->invitation->inviteable->save();
    }
}
