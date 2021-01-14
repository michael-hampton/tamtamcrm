<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\CaseOverdue;
use App\Models\Cases;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CaseOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var Cases
     */
    private Cases $case;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * CaseOverdueNotification constructor.
     * @param Cases $case
     * @param string $message_type
     */
    public function __construct(Cases $case, $message_type = '')
    {
        $this->case = $case;
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
     * @return CaseOverdue
     */
    public function toMail($notifiable)
    {
        return new CaseOverdue($this->case, $notifiable);
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
                                 ->from("System")->image($this->case->account->present()->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        $this->subject = trans(
            'texts.notification_case_overdue_subject',
            [
                'customer' => $this->case->customer->name,
                'number'   => $this->case->number,
                'due_date' => $this->case->due_date
            ]
        );
    }

}
