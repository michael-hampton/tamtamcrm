<?php

namespace App\Listeners\Invoice;

use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class InvoiceCreatedNotification implements ShouldQueue
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
        foreach ($event->invoice->account->account_users as $account_user) {

            $notification_types = $this->findUserNotificationTypesByEntity($event->invoice, $account_user, ['invoice_created']);

            if (empty($notification_types)) {
                continue;
            }

            if (in_array('mail', $notification_types)) {
                $account_user->user->notify(new \App\Notifications\Invoice\InvoiceCreatedNotification($event->invoice, 'mail'));
            }

            if (!empty($event->invoice->account->slack_webhook_url) && in_array('slack', $notification_types)) {
                Notification::route('slack', $event->invoice->account->slack_webhook_url)->notify(
                    new \App\Notifications\Invoice\InvoiceCreatedNotification($event->invoice, 'slack')
                );
            }
        }
    }
}
