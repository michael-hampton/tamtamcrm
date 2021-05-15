<?php

namespace App\Listeners\Lead;

use App\Services\Email\DispatchEmail;
use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeadCreated implements ShouldQueue
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
        $data = [
            'id'      => $event->lead->id,
            'message' => 'A lead was created'
        ];

        $fields = [
            'notifiable_id'   => $event->lead->user_id,
            'account_id'      => $event->lead->account_id,
            'notifiable_type' => get_class($event->lead),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'created'
        ];

        $notification = NotificationFactory::create($event->lead->account_id, $event->lead->user_id);
        $notification->entity_id = $event->lead->id;
        $this->notification_repo->save($notification, $fields);

        (new DispatchEmail($event->lead))->execute(
            null,
            $event->lead->account->getSetting('email_subject_lead'),
            $event->lead->account->getSetting('email_template_lead')
        );
    }
}
