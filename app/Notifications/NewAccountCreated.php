<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $is_system;
    /**
     * Create a new notification instance.
     *
     * @return void
     */

    protected $user;
    protected $account;

    public function __construct($user, $account, $is_system = false)
    {
        $this->user = $user;
        $this->account = $account;
        $this->is_system = $is_system;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $user_name = $this->user->first_name . " " . $this->user->last_name;
        $email = $this->user->email;
        $ip = $this->user->ip;

        $data = [
            'title'       => trans('texts.new_account_created'),
            'message'     => trans('texts.new_account_text', ['user' => $user_name, 'email' => $email, 'ip' => $ip]),
            'url'         => config('taskmanager.web_url'),
            'button_text' => trans('texts.login'),
            'signature'   => $this->account->settings->email_signature,
            'logo'        => $this->account->present()->logo(),
        ];


        return (new MailMessage)->subject(trans('texts.new_account_created'))->markdown('email.admin.new', $data);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [//
        ];
    }

    public function toSlack($notifiable)
    {
        $this->user->setAccount($this->account);

        $user_name = $this->user->first_name . " " . $this->user->last_name;
        $email = $this->user->email;
        $ip = $this->user->ip;

        return (new SlackMessage)->success()->from(trans('texts.from_slack'))
                                 ->content(
                                     "A new account has been created by {$user_name} - {$email} - from IP: {$ip}"
                                 );
    }
}
