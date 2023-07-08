<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ForceLogout
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
        if (Auth::check()) {
            if ($request->session()->has('password')) {
                $password = $request->session()->get('password');

                if ($password != Auth::user()->password) {
                    Auth::logout();
    
                    return redirect()->route('home');
                }
                
                $request->session()->get('password');
            } else {
                $request->session()->put('password', Auth::user()->password);
            }
        }

        return $next($request);
    }
}
