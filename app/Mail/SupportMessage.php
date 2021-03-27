<?php

namespace App\Mail;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMessage extends Mailable
{
    use Queueable, SerializesModels;

    private $message;

    private Account $account;

    /**
     * SupportMessage constructor.
     * @param Account $account
     * @param $message
     */
    public function __construct(Account $account, $message)
    {
        $this->message = $message;
        $this->account = $account;
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
                                'title'       => trans('texts.support_ticket_subject'),
                                'message'     => $this->message,
                                'show_footer' => empty($this->account->domains->plan) || !in_array(
                                        $this->account->domains->plan->code,
                                        ['PROM', 'PROY']
                                    )
                            ]
                        ]
                    );
    }
}
