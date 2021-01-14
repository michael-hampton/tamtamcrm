<?php

namespace App\Listeners\PurchaseOrder;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseOrderEmailed implements ShouldQueue
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
        $fields['data']['id'] = $event->invitation->inviteable->id;
        $fields['data']['contact_id'] = $event->invitation->contact_id;
        $fields['data']['company_id'] = $event->invitation->inviteable->company_id;
        $fields['data']['message'] = 'A purchase order was emailed';
        $fields['notifiable_id'] = $event->invitation->inviteable->user_id;
        $fields['account_id'] = $event->invitation->inviteable->account_id;
        $fields['notifiable_type'] = get_class($event->invitation->inviteable);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create(
            $event->invitation->inviteable->account_id,
            $event->invitation->inviteable->user_id
        );
        $notification->entity_id = $event->invitation->inviteable->id;
        $this->notification_repo->save($notification, $fields);
    }
}
