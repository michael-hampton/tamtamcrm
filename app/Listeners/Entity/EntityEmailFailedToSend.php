<?php

namespace App\Listeners\Entity;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;

class EntityEmailFailedToSend implements ShouldQueue
{
    use UserNotifies;

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
        $entity_name = $event->entity;
        $errors = $event->errors;

        $fields = [];
        $fields['data']['id'] = $event->entity->id;
        $fields['data']['customer_id'] = !empty($event->entity->customer_id) ? $event->entity->customer_id : null;
        $fields['data']['message'] = 'An email failed to send';
        $fields['notifiable_id'] = $event->entity->user_id;
        $fields['account_id'] = $event->entity->account_id;
        $fields['notifiable_type'] = get_class($event->entity);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification =
            NotificationFactory::create($event->entity->account_id, $event->entity->user_id);
        $notification->entity_id = $event->entity->id;
        $this->notification_repo->save($notification, $fields);
    }
}
