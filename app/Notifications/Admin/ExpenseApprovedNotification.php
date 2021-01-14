<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\ExpenseApproved;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ExpenseApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var Expense
     */
    private Expense $expense;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * ExpenseApprovedNotification constructor.
     * @param Expense $expense
     * @param string $message_type
     */
    public function __construct(Expense $expense, $message_type = '')
    {
        $this->expense = $expense;
        $this->message_type = $message_type;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return !empty($this->message_type)
            ? [$this->message_type]
            : [
                $notifiable->account_user()->default_notification_type
            ];
    }

    /**
     * @param $notifiable
     * @return ExpenseApproved
     */
    public function toMail($notifiable)
    {
        return new ExpenseApproved($this->expense, $notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [//
        ];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)->success()
                                 ->from("System")->image($this->expense->account->present()->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        $this->subject = trans(
            'texts.notification_expense_approved_subject',
            [
                'total' => $this->expense->getFormattedTotal(),
                'quote' => $this->expense->getNumber(),
            ]
        );
    }

}
