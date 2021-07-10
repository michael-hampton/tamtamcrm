<?php

namespace App\Listeners\RecurringInvoice;

use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class RecurringInvoiceCreatedNotification implements ShouldQueue
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
        foreach ($event->recurring_invoice->account->account_users as $account_user) {

            $notification_types = $this->findUserNotificationTypesByEntity($event->recurring_invoice, $account_user, ['recurring_invoice_created']);

            if (empty($notification_types)) {
                continue;
            }

            if (in_array('mail', $notification_types)) {
                $account_user->user->notify(new \App\Notifications\RecurringInvoice\RecurringInvoiceCreatedNotification($event->recurring_invoice, 'mail'));
            }

            if (!empty($event->recurring_invoice->account->slack_webhook_url) && in_array('slack', $notification_types)) {
                Notification::route('slack', $event->recurring_invoice->account->slack_webhook_url)->notify(
                    new \App\Notifications\RecurringInvoice\RecurringInvoiceCreatedNotification($event->recurring_invoice, 'slack')
                );
            }
        }
    }


}
