<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class Reauthenticate
{

    public function handle($request, Closure $next)
    {
        if (empty(auth()->user())) {
            return response()->json('User not found', 412);
        }

        $default_logout_time = auth()->user()->account_user()->account->settings->password_timeout;
        $logout_time = $default_logout_time * 60; // to seconds

        if (strtotime('now') - Cache::get('reauthenticate_last_authentication', 0) > $logout_time) {
            if (empty($request->input('password'))) {
                return response()->json('Password missing', 412);
            }

            if (!Hash::check($request->input('password'), auth()->user()->password)) {
                return response()->json('Invalid password', 412);
            }

            Cache::put('reauthenticate_last_authentication', strtotime('now'));
        }

        return $next($request);
    }
}