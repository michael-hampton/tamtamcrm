<?php


namespace App\Notifications\User;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewDevice extends Notification implements ShouldQueue
{

    use Queueable;

    /**
     * @var array
     */
    private array $authentication_log;

    /**
     * NewDevice constructor.
     * @param array $authentication_log
     */
    public function __construct(array $authentication_log)
    {
        $this->authentication_log = $authentication_log;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(trans('texts.login_notification_subject'))
            ->markdown('email.admin.login', [
                'account'   => $notifiable,
                'time'      => $this->authentication_log['login_at'],
                'ipAddress' => $this->authentication_log['ip_address'],
                'browser'   => $this->authentication_log['user_agent'],
            ]);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->from(config('app.name'))
            ->warning()
            ->content(trans('texts.login_notification_message', ['app' => config('app.name')]))
            ->attachment(function ($attachment) use ($notifiable) {
                $attachment->fields([
                    'Account'    => $notifiable->email,
                    'Time'       => $this->authentication_log['login_at']->toCookieString(),
                    'IP Address' => $this->authentication_log['ip_address'],
                    'Browser'    => $this->authentication_log['user_agent'],
                ]);
            });
    }
}