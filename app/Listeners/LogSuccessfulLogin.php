<?php


namespace App\Listeners;


use App\Notifications\User\NewDevice;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    public $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function handle(Login $event)
    {
        $user = $event->user;
        $ip = $this->request->ip();
        $user_agent = $this->request->userAgent();
        $known = $user->whereIpAddress($ip)->whereUserAgent($user_agent)->first();
        $new_user = Carbon::parse($user->created_at)->diffInMinutes(Carbon::now()) < 1;

        $user->ip_address = $ip;
        $user->user_agent = $user_agent;
        $user->save();

        $authentication_log = [
            'ip_address' => $ip,
            'user_agent' => $user_agent,
            'login_at' => Carbon::now()
        ];

        if(!$known && !$new_user && config('taskmanager.notify_on_login')) {
            $user->notify(new NewDevice($authentication_log));
        }
    }
}