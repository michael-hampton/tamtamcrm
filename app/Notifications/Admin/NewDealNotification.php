<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\DealCreated;
use App\Traits\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewDealNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use Money;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    private $deal;
    private string $message_type;

    /**
     * NewDealNotification constructor.
     * @param $deal
     * @param string $message_type
     */
    public function __construct($deal, string $message_type = '')
    {
        $this->deal = $deal;
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
     * @return DealCreated
     */
    public function toMail($notifiable)
    {
        return new DealCreated($this->deal, $notifiable);
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
        $logo = $this->deal->account->present()->logo();

        return (new SlackMessage)->success()
                                 ->from("System")->image($logo)->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        $this->subject = trans(
            'texts.notification_deal_subject',
            [
                'total'    => $this->formatCurrency($this->deal->valued_at, $this->deal->customer),
                'customer' => $this->deal->customer->present()->name()
            ]
        );
    }

}
