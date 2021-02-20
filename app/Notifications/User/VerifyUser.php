<?php

namespace App\Notifications\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyUser extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('texts.user_confirmed_subject'))
            ->markdown(
                'email.admin.new',
                [
                    'data' => [
                        'title'       => trans('texts.user_confirmed_subject'),
                        'message'     => trans('texts.user_confirmed_body'),
                        'button_text' => trans('texts.user_confirmed_button'),
                        'url'         => url("/user/confirm/{$this->user->confirmation_code}")
                    ]
                ]
            );
    }
}
