<?php

namespace App\Listeners\PurchaseOrder;

use App\Notifications\Admin\EntitySentNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseOrderEmailedNotification implements ShouldQueue
{

    use UserNotifies;

    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $invitation = $event->invitation;

        foreach ($invitation->account->account_users as $account_user) {
            $user = $account_user->user;

            $notification = new EntitySentNotification($invitation, 'purchase_order', $account_user);

            $notification->method = $this->findUserNotificationTypesByInvitation(
                $invitation,
                $account_user,
                'purchase_order',
                ['all_notifications', 'purchase_order_sent']
            );

            $user->notify($notification);
        }
    }


}
