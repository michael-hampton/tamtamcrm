<?php

namespace App\Listeners\Invoice;

use App\Notifications\Admin\EntitySentNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceEmailedNotification implements ShouldQueue
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

            $notification = new EntitySentNotification($invitation, 'invoice', $account_user);

            $notification->method = $this->findUserNotificationTypesByInvitation(
                $invitation,
                $account_user,
                'invoice',
                ['all_notifications', 'invoice_sent']
            );

            $user->notify($notification);
        }
    }


}
