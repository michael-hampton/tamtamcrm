<?php

namespace App\Mail;


use Illuminate\Mail\Mailable;

class TestMail extends Mailable
{
    public $sender;
    public $subject;
    public $body;

    public function __construct($sender, $subject, $body)
    {
        $this->sender = $sender;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function build()
    {
        return $this
            ->from($this->sender, 'Michael Hampton')
            ->attach(public_path('files/admin.jpg'))
            ->subject($this->subject)
            ->markdown('email.testmail');
    }
}
