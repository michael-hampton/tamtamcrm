<?php

namespace App\Listeners\Project;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectArchived implements ShouldQueue
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
        $fields['data']['id'] = $event->project->id;
        $fields['data']['customer_id'] = $event->project->customer_id;
        $fields['data']['message'] = 'A project was archived';
        $fields['notifiable_id'] = $event->project->user_id;
        $fields['account_id'] = $event->project->account_id;
        $fields['notifiable_type'] = get_class($event->project);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->project->account_id, $event->project->user_id);
        $notification->entity_id = $event->project->id;
        $this->notification_repo->save($notification, $fields);
    }
}
