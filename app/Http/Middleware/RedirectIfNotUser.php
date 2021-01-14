<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotUser
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'user')
    {
        if (!auth()->guard($guard)->check()) {
            $request->session()->flash('error', 'You must be an user to see this page');
            return redirect(route('admin.login'));
        }
        return $next($request);
    }

}
