<?php

namespace App\Listeners\User;

use App\Events\User\UserEmailChanged;
use App\Mail\UserEmailChangedNotification;
use Illuminate\Support\Facades\Mail;

class SendUserEmailChangedEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserEmailChanged $event
     * @return void
     */
    public function handle(UserEmailChanged $event)
    {
        Mail::to($event->user->email)->send(
            new UserEmailChangedNotification($event->user)
        );
    }
}
