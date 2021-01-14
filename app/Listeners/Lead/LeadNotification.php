<?php

namespace App\Listeners\Lead;

use App\Notifications\Admin\NewLeadNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class LeadNotification implements ShouldQueue
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
        $lead = $event->lead;

        if (!empty($lead->account->account_users)) {
            foreach ($lead->account->account_users as $account_user) {
                $notification_types = $this->getNotificationTypesForAccountUser(
                    $account_user,
                    ['lead_success']
                );

                if (!empty($notification_types) && in_array('mail', $notification_types)) {
                    $account_user->user->notify(new NewLeadNotification($lead, 'mail'));
                }
            }
        }

        if (!empty($lead->account->slack_webhook_url)) {
            Notification::route('slack', $lead->account->slack_webhook_url)->notify(
                new NewLeadNotification(
                    $lead,
                    'slack'
                )
            );
        }
    }
}
