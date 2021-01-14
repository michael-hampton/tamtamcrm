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
            foreach ($expense->account->account_users as $account_user) {
                $notification_types = $this->getNotificationTypesForAccountUser(
                    $account_user,
                    ['expense_approved']
                );

                if (!empty($notification_types) && in_array('mail', $notification_types)) {
                    $account_user->user->notify(new PurchaseOrderApprovedNotification($expense, 'mail'));
                }
            }
        }

        if (isset($expense->account->slack_webhook_url)) {
            Notification::route('slack', $expense->account->slack_webhook_url)->notify(
                new ExpenseApprovedNotification(
                    $expense,
                    'slack'
                )
            );
        }
    }
}