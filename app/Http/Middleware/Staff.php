<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Staff
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
        if (Auth::check() && Auth::user()->isStaff()) {
            return $next($request);
        }
    
        $request->session()->flash('notify', ['type' => 'error', 'message' => "Vous n'etes pas autorisé a accéder à cette page"]);
        return redirect()->route('home');
    }
}
