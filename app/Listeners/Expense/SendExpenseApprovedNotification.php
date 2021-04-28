<?php


namespace App\Listeners\Expense;


use App\Notifications\Admin\ExpenseApprovedNotification;
use App\Notifications\Admin\PurchaseOrderApprovedNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Support\Facades\Notification;

class SendExpenseApprovedNotification
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
        $expense = $event->expense;

        if (!empty($expense->account->account_users)) {
            foreach ($event->invoice->account->account_users as $account_user) {

                $notification_types = $this->findUserNotificationTypesByEntity($event->invoice, $account_user, ['expense_approved']);

                if (empty($notification_types)) {
                    continue;
                }

                if (in_array('mail', $notification_types)) {
                    $account_user->user->notify(new ExpenseApprovedNotification($event->invoice, 'mail'));
                }

                if (!empty($event->invoice->account->slack_webhook_url) && in_array('slack', $notification_types)) {
                    Notification::route('slack', $event->expense->account->slack_webhook_url)->notify(
                        new ExpenseApprovedNotification($event->expense, 'slack')
                    );
                }
            }
        }
    }
}