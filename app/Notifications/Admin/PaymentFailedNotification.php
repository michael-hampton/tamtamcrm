<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\PaymentFailed;
use App\Models\Payment;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var Payment
     */
    private Payment $payment;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * NewPaymentNotification constructor.
     * @param Payment $payment
     * @param string $message_type
     */
    public function __construct(Payment $payment, $message_type = '')
    {
        $this->payment = $payment;
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
     * @return PaymentFailed
     */
    public function toMail($notifiable)
    {
        return new PaymentFailed($this->payment, $notifiable);
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
                                 ->from("System")->image((new AccountViewModel($this->payment->account))->logo())->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        $this->subject = trans(
            'texts.notification_payment_failed_subject',
            ['customer' => (new CustomerViewModel($this->payment->customer))->name()]
        );
    }

}
