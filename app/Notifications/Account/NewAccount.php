<?php

namespace App\Notifications\Account;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewAccount extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('texts.new_account_created_subject'))
            ->markdown(
                'email.admin.new',
                [
                    'data' => [
                        'title'       => trans('texts.new_account_created_subject'),
                        'message'     => trans('texts.new_account_created_body'),
                        'button_text' => trans('texts.new_account_created_button'),
                        'url'         => url(config('taskmanager.site_url')),
                        'signature'   => isset($this->account->settings->email_signature) ? $this->account->settings->email_signature : '',
                        'logo'        => $this->account->present()->logo(),
                    ]
                ]
            );
    }
}
