<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckUserTicket
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
        if (Auth::check() && !Auth::user()->ticket) {
            Auth::user()->ticket = str_random(32);
            Auth::user()->save();
        }

        return $next($request);
    }
}
