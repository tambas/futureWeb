<?php

namespace App\Http\Middleware;

use Closure;
use \Config;

class FillSettings
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
        $json = json_decode(@file_get_contents("../settings.json"));

        if (!$json) {
            Config::set('dofus.motd', null);
            Config::set('dofus.welcome', null);
            return $next($request);
        }

        if (isset($json->motd)) {
            $motd = [];

            $motd['title']    = @$json->motd->title;
            $motd['subtitle'] = @$json->motd->subtitle;
            $motd['post_id']  = @$json->motd->post_id;

            Config::set('dofus.motd', $motd);
        }

        if (isset($json->welcome)) {
            $welcome = [];
            
            $welcome['message']    = @$json->welcome->message;

            Config::set('dofus.welcome', $welcome);
        }

        if (isset($json->theme)) {
            $theme = [];

            $theme['background'] = @$json->theme->background;
            $theme['color']      = @$json->theme->color;
            $theme['animated']   = @$json->theme->animated;

            Config::set('dofus.theme', $theme);
        }

        return $next($request);
    }
}
