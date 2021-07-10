<?php

namespace App\Listeners\PurchaseOrder;

use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class PurchaseOrderCreatedNotification implements ShouldQueue
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
        foreach ($event->purchase_order->account->account_users as $account_user) {

            $notification_types = $this->findUserNotificationTypesByEntity($event->purchase_order, $account_user, ['purchase_order_created']);

            if (empty($notification_types)) {
                continue;
            }

            if (in_array('mail', $notification_types)) {
                $account_user->user->notify(new \App\Notifications\PurchaseOrder\PurchaseOrderCreatedNotification($event->purchase_order, 'mail'));
            }

            if (!empty($event->purchase_order->account->slack_webhook_url) && in_array('slack', $notification_types)) {
                Notification::route('slack', $event->purchase_order->account->slack_webhook_url)->notify(
                    new \App\Notifications\PurchaseOrder\PurchaseOrderCreatedNotification($event->purchase_order, 'slack')
                );
            }
        }
    }


}
