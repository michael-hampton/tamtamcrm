<?php

namespace App\Notifications\RecurringQuote;

use App\Mail\Admin\EntityCreated;
use App\Models\Order;
use App\Models\RecurringQuote;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class RecurringQuoteCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var RecurringQuote
     */
    private RecurringQuote $recurring_quote;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * QuoteCreatedNotification constructor.
     * @param RecurringQuote $recurring_quote
     * @param string $message_type
     */
    public function __construct(RecurringQuote $recurring_quote, $message_type = '')
    {
        $this->recurring_quote = $recurring_quote;
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
        return new EntityCreated($this->recurring_quote, 'recurring_quote', $notifiable);
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
                                 ->from("System")->image((new AccountViewModel($this->recurring_quote->account))->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        return trans(
            'texts.notification_credit_created_subject',
            [
                'total'           => $this->recurring_quote->getFormattedTotal(),
                'recurring_quote' => $this->recurring_quote->getNumber(),
                'customer'        => (new CustomerViewModel($this->recurring_quote->customer))->name()
            ]
        );
    }

}
