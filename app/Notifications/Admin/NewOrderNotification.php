<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\OrderCreated;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * NewOrderNotification constructor.
     * @param Order $order
     * @param string $message_type
     */
    public function __construct(Order $order, string $message_type = '')
    {
        $this->order = $order;
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
     * @return OrderCreated
     */
    public function toMail($notifiable)
    {
        return new OrderCreated($this->order, $notifiable);
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
        $logo = $this->order->account->present()->logo();

        return (new SlackMessage)->success()
                                 ->from("System")->image($logo)->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        $this->subject = trans(
            'texts.notification_order_subject',
            [
                'total'    => $this->order->getFormattedTotal(),
                'customer' => $this->order->customer->present()->name(),
                'order'    => $this->order->getNumber(),
            ]
        );
    }

}

