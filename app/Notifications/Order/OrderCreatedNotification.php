<?php

namespace App\Notifications\Order;

use App\Mail\Admin\EntityCreated;
use App\Models\Order;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
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
     * OrderCreatedNotification constructor.
     * @param Order $order
     * @param string $message_type
     */
    public function __construct(Order $order, $message_type = '')
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
     * @return EntityCreated
     */
    public function toMail($notifiable)
    {
        return new EntityCreated($this->order, 'order', $notifiable);
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
                                 ->from("System")->image((new AccountViewModel($this->quote->account))->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        return trans(
            'texts.notification_order_created_subject',
            [
                'total'    => $this->order->getFormattedTotal(),
                'order'    => $this->order->getNumber(),
                'customer' => (new CustomerViewModel($this->order->customer))->name()
            ]
        );
    }

}
