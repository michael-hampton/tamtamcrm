<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMessage extends Mailable
{
    use Queueable, SerializesModels;

    private $message;

    /**
     * Create a new message instance.
     *
     * @param $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('tamtamcrm@support.com')
                    ->subject(trans('texts.support_ticket_subject'))
                    ->markdown(
                        'email.admin.new',
                        [
                            'data' => [
                                'title'   => trans('texts.support_ticket_subject'),
                                'message' => $this->message
                            ]
                        ]
                    );
    }
}
