<?php

namespace App\Listeners\Company;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class CompanyCreated implements ShouldQueue
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
        $fields['data']['id'] = $event->company->id;
        $fields['data']['message'] = 'A company was created';
        $fields['notifiable_id'] = $event->company->user_id;
        $fields['account_id'] = $event->company->account_id;
        $fields['notifiable_type'] = get_class($event->company);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->company->account_id, $event->company->user_id);
        $notification->entity_id = $event->company->id;
        $this->notification_repo->save($notification, $fields);
    }
}
