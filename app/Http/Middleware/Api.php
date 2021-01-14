<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class API
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header(
            'Access-Control-Allow-Headers',
            'Origin, Content-Type, Content-Range, Content-Disposition, Content-Description, X-Auth-Token'
        );
        //$response->header('Access-Control-Allow-Origin', '*');
        //add more headers here
        return $response;
    }

}
