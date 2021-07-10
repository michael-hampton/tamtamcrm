<?php

namespace App\Listeners\Cases;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaseEmailed implements ShouldQueue
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
            'id'          => $event->case->id,
            'customer_id' => $event->case->customer_id,
            'message'     => 'A case was emailed'
        ];

        $fields = [
            'notifiable_id'   => $event->case->user_id,
            'account_id'      => $event->case->account_id,
            'notifiable_type' => get_class($event->case),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'emailed'
        ];

        $notification = NotificationFactory::create($event->case->account_id, $event->case->user_id);
        $notification->entity_id = $event->case->id;
        $this->notification_repo->save($notification, $fields);
    }
}
