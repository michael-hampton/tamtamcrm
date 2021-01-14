<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\PurchaseOrderApproved;
use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderApprovedNotification extends Notification implements ShouldQueue
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
     * SendPurchaseOrderApprovedNotification constructor.
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
     * @return PurchaseOrderApproved
     */
    public function toMail($notifiable)
    {
        return new PurchaseOrderApproved($this->purchase_order, $notifiable);
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
                                 ->from("System")->image($this->purchase_order->account->present()->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        $this->subject = trans(
            'texts.notification_purchase_order_approved_subject',
            [
                'total'          => $this->purchase_order->getFormattedTotal(),
                'purchase_order' => $this->purchase_order->getNumber(),
            ]
        );
    }

}
