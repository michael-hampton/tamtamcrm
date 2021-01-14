<?php

namespace App\Listeners\Cases;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaseArchived implements ShouldQueue
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
        $fields['data']['id'] = $event->case->id;
        $fields['data']['customer_id'] = $event->case->customer_id;
        $fields['data']['message'] = 'A case was archived';
        $fields['notifiable_id'] = $event->case->user_id;
        $fields['account_id'] = $event->case->account_id;
        $fields['notifiable_type'] = get_class($event->case);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->case->account_id, $event->case->user_id);
        $notification->entity_id = $event->case->id;
        $this->notification_repo->save($notification, $fields);
    }
}
