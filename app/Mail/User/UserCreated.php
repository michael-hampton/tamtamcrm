<?php

namespace App\Mail\User;

use App\Models\User;
use App\ViewModels\AccountViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

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
        if (empty($user)) {
            $user = User::first();
        }

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

        if (empty($this->user->email)) {
            Log::emergency('empty email');
            $this->user->email = 'michaelhamptondesign@yahoo.com';
        }

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
            'texts.new_user_created_subject',
            $this->buildDataArray()
        );
    }

    private function buildDataArray()
    {
        return [
            'email' => $this->user->email
        ];
    }

    private function setMessage()
    {
        $this->message = trans(
            'texts.new_user_created_body',
            $this->buildDataArray()

        );
    }

    private function buildMessage()
    {
        $account = !empty($this->user->account_user()) ? $this->user->account_user()->account : $this->user->accounts->first();

        $this->message_array = [
            'title' => $this->subject,
            'message' => $this->message,
            'logo' => (new AccountViewModel($account))->logo(),
            'url' => $this->url,
            'button_text' => trans('texts.new_user_created_button'),
            'show_footer' => empty($this->user->domain->plan) || !in_array(
                    $this->user->domain->plan->code,
                    ['PROM', 'PROY']
                )
        ];
    }


}
