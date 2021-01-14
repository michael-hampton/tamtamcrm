<?php

namespace App\Listeners\Task;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskArchived implements ShouldQueue
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
        $fields['data']['id'] = $event->task->id;
        $fields['data']['customer_id'] = $event->task->customer_id;
        $fields['data']['message'] = 'A task was archived';
        $fields['notifiable_id'] = $event->task->user_id;
        $fields['account_id'] = $event->task->account_id;
        $fields['notifiable_type'] = get_class($event->task);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->task->account_id, $event->task->user_id);
        $notification->entity_id = $event->task->id;
        $this->notification_repo->save($notification, $fields);
    }
}
