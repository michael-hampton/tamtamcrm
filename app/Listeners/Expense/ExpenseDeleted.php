<?php

namespace App\Listeners\Expense;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpenseDeleted implements ShouldQueue
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
        $fields['data']['id'] = $event->expense->id;
        $fields['data']['customer_id'] = $event->expense->customer_id;
        $fields['data']['message'] = 'A expense was deleted';
        $fields['notifiable_id'] = $event->expense->user_id;
        $fields['account_id'] = $event->expense->account_id;
        $fields['notifiable_type'] = get_class($event->expense);
        $fields['type'] = get_class($this);
        $fields['data'] = json_encode($fields['data']);

        $notification = NotificationFactory::create($event->expense->account_id, $event->expense->user_id);
        $notification->entity_id = $event->expense->id;
        $this->notification_repo->save($notification, $fields);
    }
}
