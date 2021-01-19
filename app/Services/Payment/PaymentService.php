<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Services\ServiceBase;

class PaymentService extends ServiceBase
{
    /**
     * @var Payment
     */
    private Payment $payment;

    /**
     * PaymentService constructor.
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        parent::__construct($payment);
        $this->payment = $payment;
    }

    public function sendEmail()
    {
        $subject = $this->payment->customer->getSetting('email_subject_payment');
        $body = $this->payment->customer->getSetting('email_template_payment');

        foreach ($this->payment->customer->contacts as $contact) {
            $footer = ['link' => $this->payment->getUrl(), 'text' => trans('texts.view_payment')];
            $this->dispatchEmail($contact, $subject, $body, 'email_template_payment', $footer);
        }
    }

    public function generatePdf()
    {
        //TODO
    }
}
