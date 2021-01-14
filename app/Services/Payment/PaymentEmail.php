<?php

namespace App\Services\Payment;

use App\Jobs\Email\SendEmail;
use App\Models\Payment;

class PaymentEmail
{

    /**
     * @var Payment
     */
    private Payment $payment;

    /**
     * PaymentEmail constructor.
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $subject = $this->payment->customer->getSetting('email_subject_payment');
        $body = $this->payment->customer->getSetting('email_template_payment');

        $this->payment->customer->contacts->each(
            function ($contact) use ($subject, $body) {
                if ($contact->send_email && $contact->email) {
                    SendEmail::dispatchNow($this->payment, $subject, $body, 'payment', $contact);
                }
            }
        );

        return true;
    }
}
