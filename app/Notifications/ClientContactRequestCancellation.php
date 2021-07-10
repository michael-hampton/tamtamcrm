<?php

namespace App\Notifications;

use App\Models\CustomerContact;
use App\Models\RecurringInvoice;
use App\ViewModels\CustomerContactViewModel;
use App\ViewModels\CustomerViewModel;
use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laracasts\Presenter\Exceptions\PresenterException;

class ClientContactRequestCancellation extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var Closure|null
     */
    public static $toMailCallback;
    /**
     * Create a new notification instance.
     *
     * @return void
     */

    /**
     * @var RecurringInvoice
     */
    protected RecurringInvoice $recurring_invoice;

    /**
     * @var CustomerContact
     */
    protected CustomerContact $customer_contact;

    /**
     * ClientContactRequestCancellation constructor.
     * @param RecurringInvoice $recurring_invoice
     * @param CustomerContact $customer_contact
     */
    public function __construct(RecurringInvoice $recurring_invoice, CustomerContact $customer_contact)
    {
        $this->recurring_invoice = $recurring_invoice;
        $this->customer_contact = $customer_contact;
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param Closure $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     * @throws PresenterException
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->customer_contact);
        }


        $contactViewModel = new CustomerContactViewModel($this->customer_contact);
        $customer_contact_name = $contactViewModel->name();
        $customer_name = (new CustomerViewModel($this->customer_contact->customer))->name();
        $recurring_invoice_number = $this->recurring_invoice->number;


        return (new MailMessage)
            ->subject('Request for recurring invoice cancellation from ' . $customer_contact_name)
            ->markdown(
                'email.support.cancellation',
                [
                    'message' => "Contact [{$customer_contact_name}] from Customer [{$customer_name}] requested to cancel Recurring Invoice [#{$recurring_invoice_number}]",
                ]
            );
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
            //
        ];
    }

    public function toSlack($notifiable)
    {
        $name = (new CustomerContactViewModel($this->customer_contact))->name();
        $customer_name = (new CustomerViewModel($this->customer_contact->customer))->name();
        $recurring_invoice_number = $this->recurring_invoice->number;

        return (new SlackMessage)
            ->from("System")
            ->content(
                "Contact {$name} from customer {$customer_name} requested to cancel Recurring Invoice #{$recurring_invoice_number}"
            );
    }
}