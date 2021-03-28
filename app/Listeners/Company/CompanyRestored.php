<?php

namespace App\Listeners\Company;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CompanyRestored implements ShouldQueue
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
            'id'      => $event->company->id,
            'message' => 'A company was restored'
        ];

        $fields = [
            'notifiable_id'   => $event->company->user_id,
            'account_id'      => $event->company->account_id,
            'notifiable_type' => get_class($event->company),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'restored'
        ];

        $notification = NotificationFactory::create($event->company->account_id, $event->company->user_id);
        $notification->entity_id = $event->company->id;
        $this->notification_repo->save($notification, $fields);
    }
}
