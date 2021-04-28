<?php

namespace App\Notifications\Credit;

use App\Mail\Admin\EntityCreated;
use App\Models\Order;
use App\Models\Credit;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CreditCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var Credit
     */
    private Credit $credit;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * QuoteCreatedNotification constructor.
     * @param Credit $credit
     * @param string $message_type
     */
    public function __construct(Credit $credit, $message_type = '')
    {
        $this->credit = $credit;
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
     * @return EntityCreated
     */
    public function toMail($notifiable)
    {
        return new EntityCreated($this->credit, 'credit', $notifiable);
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
                                 ->from("System")->image((new AccountViewModel($this->credit->account))->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        return trans(
            'texts.notification_credit_created_subject',
            [
                'total'    => $this->credit->getFormattedTotal(),
                'credit'   => $this->credit->getNumber(),
                'customer' => (new CustomerViewModel($this->credit->customer))->name()
            ]
        );
    }

}
