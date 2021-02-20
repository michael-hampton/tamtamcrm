<?php

namespace App\Http\Middleware;

use App\Mail\TwoFactorAuthMail;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorVerification
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, bool $two_factor_enabled = false)
    {
        $user = auth()->user();

        if ($user->two_factor_expiry > Carbon::now(
            ) || (!$two_factor_enabled && !$user->two_factor_authentication_enabled)) {
            return $next($request);
        }

        $google2fa = new Google2FA();
        $user->two_factor_token = encrypt($google2fa->generateSecretKey());
        $user->two_factor_authentication_enabled = true;
        $user->save();

        Mail::to($user)->send(new TwoFactorAuthMail($user->two_factor_token));
        //Twilio::message($user->phone_number, 'Two Factor Code: ' . $user->two_factor_token);
        return redirect('/2fa');
    }
}
