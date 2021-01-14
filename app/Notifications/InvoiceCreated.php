<?php

namespace App\Notifications;

use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreated extends Notification
{
    use Queueable;

    private $invoice;

    private $account;

    /**
     * Create a new notification instance.
     *
     * @param Invoice $invoice
     * @param Account $account
     */
    public function __construct(Invoice $invoice, Account $account)
    {
        $this->invoice = $invoice;
        $this->account = $account;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->line('The introduction to the notification.')->action('Notification Action', url('/'))
                                ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'account_id' => $this->account->id,
            'id'         => $this->invoice->id,
            'message'    => 'A new invoice was created'
        ];
    }
}
