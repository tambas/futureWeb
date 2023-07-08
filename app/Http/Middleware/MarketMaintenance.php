<?php

namespace App\Http\Middleware;

use Closure;

class MarketMaintenance
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
        if (config('dofus.market_maintenance')) {
            $request->session()->flash('notify', ['type' => 'info', 'message' => "Le marché est en maintenance."]);
            return redirect()->to('/');
        }

        return $next($request);
    }
}
