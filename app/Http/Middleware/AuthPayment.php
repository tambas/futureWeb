<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\User;
use App\Helpers\CloudFlare;

class AuthPayment
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
        $user = User::where('ticket', $request->input('ticket'))->first();

        if ($user) {
            if (CloudFlare::ip() == $user->last_ip_address) {
                Auth::login($user);
            } else {
                //file_put_contents('bad_ip_shop.log', "ID:{$user->id} - Pseudo:{$user->pseudo} - Email:{$user->email} - LastIP: $user->last_ip_address - CloudFlare: " . CloudFlare::ip() . "\n", FILE_APPEND);
                return redirect()->route('error.fake', [5]);
            }
        } else {
            return redirect()->route('error.fake', [4]);
        }

        return $next($request);
    }
}
