<?php

namespace App\Http\Middleware;

use Closure;

class LotteryMaintenance
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
        if (config('dofus.lottery_maintenance')) {
            $request->session()->flash('notify', ['type' => 'info', 'message' => "Loterie en maintenance."]);
            return redirect()->to('/');
        }

        return $next($request);
    }
}
