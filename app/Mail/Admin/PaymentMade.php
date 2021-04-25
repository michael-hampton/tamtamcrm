<?php

namespace App\Mail\Admin;

use App\Models\Payment;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class PaymentMade extends AdminMailer
{
    use Queueable, SerializesModels;

    /**
     * @var Payment
     */
    private Payment $payment;


    /**
     * PaymentMade constructor.
     * @param Payment $payment
     * @param User $user
     */
    public function __construct(Payment $payment, User $user)
    {
        parent::__construct('payment_paid', $payment);

        $this->payment = $payment;
        $this->entity = $payment;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return void
     */
    public function build()
    {
        $data = $this->getData();

        $this->setSubject($data);
        $this->setMessage($data);
        $this->buildButton();
        $this->execute();
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'total'    => $this->payment->getFormattedTotal(),
            'customer' => (new CustomerViewModel($this->payment->customer))->name(),
            'invoice'  => $this->payment->getFormattedInvoices(),
        ];
    }

    private function buildButton(): void
    {
        $this->button = [
            'url'         => $this->getUrl() . 'payments/' . $this->payment->id,
            'button_text' => trans('texts.view_payment'),
        ];
    }
}
