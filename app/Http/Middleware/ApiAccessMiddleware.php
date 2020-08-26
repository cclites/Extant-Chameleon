<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * Note: This is a placeholder function. It is expected that API access is
     *       whitelist protected at the server level
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
