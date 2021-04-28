<?php

namespace App\Notifications\RecurringInvoice;

use App\Mail\Admin\EntityCreated;
use App\Models\Order;
use App\Models\RecurringInvoice;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class RecurringInvoiceCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var RecurringInvoice
     */
    private RecurringInvoice $recurring_invoice;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * QuoteCreatedNotification constructor.
     * @param RecurringInvoice $recurring_invoice
     * @param string $message_type
     */
    public function __construct(RecurringInvoice $recurring_invoice, $message_type = '')
    {
        $this->recurring_invoice = $recurring_invoice;
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
        return new EntityCreated($this->recurring_invoice, 'recurring_invoice', $notifiable);
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
                                 ->from("System")->image((new AccountViewModel($this->recurring_invoice->account))->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        return trans(
            'texts.notification_recurring_invoice_created_subject',
            [
                'total'             => $this->recurring_invoice->getFormattedTotal(),
                'recurring_invoice' => $this->recurring_invoice->getNumber(),
                'customer'          => (new CustomerViewModel($this->recurring_invoice->customer))->name()
            ]
        );
    }

}
