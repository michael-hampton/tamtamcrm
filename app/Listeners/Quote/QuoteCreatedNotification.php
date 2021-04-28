<?php

namespace App\Listeners\Quote;

use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class QuoteCreatedNotification implements ShouldQueue
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
        foreach ($event->quote->account->account_users as $account_user) {

            $notification_types = $this->findUserNotificationTypesByEntity($event->quote, $account_user, ['quote_created']);

            if (empty($notification_types)) {
                continue;
            }

            if (in_array('mail', $notification_types)) {
                $account_user->user->notify(new \App\Notifications\Quote\QuoteCreatedNotification($event->quote, 'mail'));
            }

            if (!empty($event->quote->account->slack_webhook_url) && in_array('slack', $notification_types)) {
                Notification::route('slack', $event->quote->account->slack_webhook_url)->notify(
                    new \App\Notifications\Quote\QuoteCreatedNotification($event->quote, 'slack')
                );
            }
        }
    }


}
