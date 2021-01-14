<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $role
     * @param null $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $permission = null)
    {
        if ($role !== 'null' && !$request->user()->userHasRole($role)) {
            abort(404);
        }

        if ($permission !== null && !$request->user()->hasPermissionTo($permission)) {
            abort(404);
        }
        return $next($request);
    }

}
