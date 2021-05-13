<?php


namespace App\Notifications\Account;


use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDataExportedNotification extends Notification
{
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /** @var string */
    public $zipFilename;

    /** @var \Illuminate\Support\Carbon */
    public $deletionDatetime;

    public function __construct(string $zipFilename)
    {
        $this->zipFilename = $zipFilename;

        $this->deletionDatetime = now()->addDays(config('personal-data-export.delete_after_days'));
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $downloadUrl = route('account-data-exports', $this->zipFilename);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $downloadUrl);
        }

        return (new MailMessage())
            ->subject(trans('texts.account_data_export_subject'))
            ->line(trans('texts.account_data_export_message'))
            ->action(trans('texts.account_data_export_button_text'), $downloadUrl)
            ->line(trans('texts.account_data_export_footer') . $this->deletionDatetime->format('Y-m-d H:i:s') . '.');
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}