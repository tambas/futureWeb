<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ShopMaintenance
{
    /**
     * Handle an incoming request.
     * If user doesn't have character or if shop is in maintenance
     * Redirect to maintenance
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('dofus.shop_maintenance')) {
            $request->session()->flash('notify', ['type' => 'warning', 'message' => "Boutique en maintenance."]);
            return redirect()->to('shop/maintenance');
        }

        return $next($request);
    }
}
