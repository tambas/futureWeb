<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\User;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = User::where('ticket', $request->header('authorizationTicket'))->first();

        if ($user) {
            Auth::login($user);
        } else {
            return response()->json(['message' => "vous n'êtes pas identifié"], 401);
        }

        return $next($request);
    }
}
