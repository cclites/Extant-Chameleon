<?php

namespace App\Http\Middleware;

use Closure;

class ApiAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Log::info("API ACCESS MIDDLEWARE");
        return $next($request);
    }
}
