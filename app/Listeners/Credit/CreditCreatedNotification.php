<?php

namespace App\Listeners\Credit;

use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class CreditCreatedNotification implements ShouldQueue
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
        foreach ($event->credit->account->account_users as $account_user) {

            $notification_types = $this->findUserNotificationTypesByEntity($event->credit, $account_user, ['credit_created']);

            if (empty($notification_types)) {
                continue;
            }

            if (in_array('mail', $notification_types)) {
                $account_user->user->notify(new \App\Notifications\Credit\CreditCreatedNotification($event->credit, 'mail'));
            }

            if (!empty($event->credit->account->slack_webhook_url) && in_array('slack', $notification_types)) {
                Notification::route('slack', $event->credit->account->slack_webhook_url)->notify(
                    new \App\Notifications\Credit\CreditCreatedNotification($event->credit, 'slack')
                );
            }
        }
    }


}
