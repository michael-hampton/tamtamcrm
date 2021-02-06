<?php

namespace App\Actions\Email;


use App\Models\Payment;

class SendPaymentEmail extends BaseEmailActions
{

    private Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        parent::__construct($payment);
    }

    public function execute()
    {
        $subject = $this->payment->customer->getSetting('email_subject_payment');
        $body = $this->payment->customer->getSetting('email_template_payment');

        foreach ($this->payment->customer->contacts as $contact) {
            $footer = ['link' => $this->payment->getUrl(), 'text' => trans('texts.view_payment')];
            $this->dispatchEmail($contact, $subject, $body, 'email_template_payment', $footer);
        }
    }
}