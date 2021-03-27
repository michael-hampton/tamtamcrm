<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private User $user;

    private $message;

    /**
     * @var array
     */
    private array $message_array;

    /**
     * @var string
     */
    private string $url;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user, string $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->setSubject();
        $this->setMessage();
        $this->buildMessage();

        return $this->to($this->user->email)
                    ->from(config('taskmanager.from_email'))
                    ->subject($this->subject)
                    ->markdown(
                        'email.admin.new',
                        [
                            'data' => $this->message_array
                        ]
                    );
    }

    private function setSubject()
    {
        $this->subject = trans(
            'texts.forgot_password_subject',
            $this->buildDataArray()
        );
    }

    private function buildDataArray()
    {
        return [
            'email' => $this->user->email,
            'count' => config('auth.passwords.users.expire')
        ];
    }

    private function setMessage()
    {
        $this->message = trans(
            'texts.forgot_password_body',
            $this->buildDataArray()

        );
    }

    private function buildMessage()
    {
        $account = !empty($this->user->account_user()) ? $this->user->account_user(
        )->account : $this->user->accounts->first();

        $this->message_array = [
            'title'       => $this->subject,
            'message'     => $this->message,
            'logo'        => $account->present()->logo(),
            'url'         => $this->url,
            'button_text' => trans('texts.forgot_password_button'),
            'show_footer' => empty($this->user->domain->plan) || !in_array(
                    $this->user->domain->plan->code,
                    ['PROM', 'PROY']
                )
        ];
    }


}
