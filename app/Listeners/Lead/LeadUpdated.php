<?php

namespace App\Listeners\Lead;

use App\Services\Pdf\GeneratePdf;
use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeadUpdated implements ShouldQueue
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
            'id'      => $event->lead->id,
            'message' => 'A lead was updated'
        ];

        $fields = [
            'notifiable_id'   => $event->lead->user_id,
            'account_id'      => $event->lead->account_id,
            'notifiable_type' => get_class($event->lead),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'updated'
        ];

        $notification = NotificationFactory::create($event->lead->account_id, $event->lead->user_id);
        $notification->entity_id = $event->lead->id;
        $this->notification_repo->save($notification, $fields);

        // regenerate pdf
        (new GeneratePdf($event->lead))->execute(null, true);
    }
}
