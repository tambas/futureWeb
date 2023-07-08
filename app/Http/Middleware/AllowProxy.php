<?php

namespace App\Http\Middleware;

use Closure;

class AllowProxy
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
        $request->setTrustedProxies(['185.61.137.100']);

        return $next($request);
    }
}
