<?php

namespace App\Notifications\Invoice;

use App\Mail\Admin\EntityCreated;
use App\Mail\Admin\QuoteApproved;
use App\Models\Invoice;
use App\Models\Quote;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var Invoice
     */
    private Invoice $invoice;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * InvoiceCreatedNotification constructor.
     * @param Invoice $invoice
     * @param string $message_type
     */
    public function __construct(Invoice $invoice, $message_type = '')
    {
        $this->invoice = $invoice;
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
        return new EntityCreated($this->invoice, 'invoice', $notifiable);
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
                                 ->from("System")->image((new AccountViewModel($this->invoice->account))->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        return trans(
            'texts.notification_invoice_created_subject',
            [
                'total'    => $this->invoice->getFormattedTotal(),
                'invoice'  => $this->invoice->getNumber(),
                'customer' => (new CustomerViewModel($this->invoice->customer))->name()
            ]
        );
    }

}
