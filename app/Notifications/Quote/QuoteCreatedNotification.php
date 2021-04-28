<?php

namespace App\Notifications\Quote;

use App\Mail\Admin\EntityCreated;
use App\Models\Order;
use App\Models\Quote;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class QuoteCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var Quote
     */
    private Quote $quote;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * QuoteCreatedNotification constructor.
     * @param Quote $quote
     * @param string $message_type
     */
    public function __construct(Quote $quote, $message_type = '')
    {
        $this->quote = $quote;
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
        return new EntityCreated($this->quote, 'quote', $notifiable);
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
            'texts.notification_quote_created_subject',
            [
                'total'    => $this->quote->getFormattedTotal(),
                'quote'    => $this->quote->getNumber(),
                'customer' => (new CustomerViewModel($this->quote->customer))->name()
            ]
        );
    }

}
