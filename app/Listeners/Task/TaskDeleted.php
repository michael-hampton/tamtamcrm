<?php

namespace App\Listeners\Task;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskDeleted implements ShouldQueue
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
            'id'          => $event->task->id,
            'customer_id' => $event->task->customer_id,
            'message'     => 'A task was deleted'
        ];

        $fields = [
            'notifiable_id'   => $event->task->user_id,
            'account_id'      => $event->task->account_id,
            'notifiable_type' => get_class($event->task),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'deleted'
        ];

        $notification = NotificationFactory::create($event->task->account_id, $event->task->user_id);
        $notification->entity_id = $event->task->id;
        $this->notification_repo->save($notification, $fields);
    }
}
