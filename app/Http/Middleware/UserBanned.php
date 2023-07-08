<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserBanned
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
        if (Auth::check() && Auth::user()->isBanned()) {
            $request->session()->flash('notify', ['type' => 'warning', 'message' => "Votre compte est banni!"]);
            Auth::logout();
            return redirect()->route('home');
        }
        return $next($request);
    }
}
