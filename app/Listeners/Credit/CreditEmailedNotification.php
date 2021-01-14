<?php

namespace App\Listeners\Credit;

use App\Notifications\Admin\EntitySentNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreditEmailedNotification implements ShouldQueue
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

            $notification = new EntitySentNotification($invitation, 'credit', $account_user);

            $notification->method = $this->findUserNotificationTypesByInvitation(
                $invitation,
                $account_user,
                'credit',
                ['all_notifications', 'credit_sent']
            );

            $user->notify($notification);
        }
    }


}
