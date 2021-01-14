<?php

namespace App\Listeners\Quote;

use App\Notifications\Admin\QuoteChangeRequestedNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendQuoteChangeRequestedNotification implements ShouldQueue
{
    use UserNotifies;

    /**
     * Create the event listener.
     *
     * @return void
     */
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
        $quote = $event->quote;

        if (!empty($quote->account->account_users)) {
            foreach ($quote->account->account_users as $account_user) {
                $notification_types = $this->getNotificationTypesForAccountUser(
                    $account_user,
                    ['notification_quote_change_requested']
                );

                if (!empty($notification_types) && in_array('mail', $notification_types)) {
                    $account_user->user->notify(new QuoteChangeRequestedNotification($quote, 'mail'));
                }
            }
        }

        if (isset($quote->account->slack_webhook_url)) {
            Notification::route('slack', $quote->account->slack_webhook_url)->notify(
                new QuoteChangeRequestedNotification(
                    $quote,
                    'slack'
                )
            );
        }
    }
}
