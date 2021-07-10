<?php

namespace App\Notifications\PurchaseOrder;

use App\Mail\Admin\EntityCreated;
use App\Mail\Admin\PurchaseOrderCreated;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var PurchaseOrder
     */
    private PurchaseOrder $purchase_order;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * PurchaseOrderCreatedNotification constructor.
     * @param PurchaseOrder $purchase_order
     * @param string $message_type
     */
    public function __construct(PurchaseOrder $purchase_order, $message_type = '')
    {
        $this->purchase_order = $purchase_order;
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
     * @return PurchaseOrderCreated
     */
    public function toMail($notifiable)
    {
        return new PurchaseOrderCreated($this->purchase_order, $notifiable);
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
                                 ->from("System")->image((new AccountViewModel($this->purchase_order->account))->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        return trans(
            'texts.notification_purchase_order_created_subject',
            [
                'total'    => $this->purchase_order->getFormattedTotal(),
                'purchase_order'    => $this->purchase_order->getNumber(),
                'customer' => (new CustomerViewModel($this->purchase_order->customer))->name()
            ]
        );
    }

}
