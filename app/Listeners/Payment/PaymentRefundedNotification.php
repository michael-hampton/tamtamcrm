<?php

namespace App\Listeners\Payment;

use App\Notifications\Admin\PaymentRefundNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class PaymentRefundedNotification implements ShouldQueue
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
        $payment = $event->payment;

        if (!empty($payment->account->account_users)) {
            foreach ($payment->account->account_users as $account_user) {
                if ($account_user->user) {
                    $notification_types = $this->getNotificationTypesForAccountUser(
                        $account_user,
                        ['payment_refunded']
                    );

                    if (!empty($notification_types) && in_array('mail', $notification_types)) {
                        $account_user->user->notify(
                            new PaymentRefundNotification($payment, 'mail')
                        );
                    }
                }
            }
        }

        if (!empty($payment->account->slack_webhook_url)) {
            Notification::route('slack', $payment->account->slack_webhook_url)
                        ->notify(new PaymentRefundNotification($payment, 'slack'));
        }
    }
}
