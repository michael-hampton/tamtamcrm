<?php

namespace App\Listeners\Deal;

use App\Notifications\Admin\NewDealNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class DealNotification implements ShouldQueue
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
        $deal = $event->deal;

        if (!empty($deal->account->account_users)) {
            foreach ($deal->account->account_users as $account_user) {
                $notification_types = $this->getNotificationTypesForAccountUser(
                    $account_user,
                    ['deal_success']
                );

                if (!empty($notification_types) && in_array('mail', $notification_types)) {
                    $account_user->user->notify(new NewDealNotification($deal, 'mail'));
                }
            }
        }

        if (!empty($deal->account->slack_webhook_url)) {
            Notification::route('slack', $deal->account->slack_webhook_url)->notify(
                new NewDealNotification(
                    $deal,
                    'slack'
                )
            );
        }
    }
}
