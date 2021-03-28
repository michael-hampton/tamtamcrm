<?php

namespace App\Listeners\Expense;

use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpenseApproved implements ShouldQueue
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
            'id'          => $event->expense->id,
            'customer_id' => $event->expense->customer_id,
            'message'     => 'A expense was approved',
            'status'      => 'approved'
        ];

        $fields = [
            'notifiable_id'   => $event->expense->user_id,
            'account_id'      => $event->expense->account_id,
            'notifiable_type' => get_class($event->expense),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'status_updated'
        ];

        $notification = NotificationFactory::create($event->expense->account_id, $event->expense->user_id);
        $notification->entity_id = $event->expense->id;
        $this->notification_repo->save($notification, $fields);
    }
}
