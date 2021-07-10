<?php


namespace App\Listeners\Order;


use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;

class OrderEmailed
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
            'id'            => $event->invitation->inviteable->id,
            'customer_id'   => $event->invitation->inviteable->customer_id,
            'invitation_id' => $event->invitation->id,
            'message'       => 'A order was emailed'
        ];

        $fields = [
            'notifiable_id'   => $event->invitation->inviteable->user_id,
            'account_id'      => $event->invitation->inviteable->account_id,
            'notifiable_type' => get_class($event->invitation->inviteable),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'emailed'
        ];

        $notification =
            NotificationFactory::create(
                $event->invitation->inviteable->account_id,
                $event->invitation->inviteable->user_id
            );
        $notification->entity_id = $event->invitation->inviteable->id;
        $this->notification_repo->save($notification, $fields);
    }
}