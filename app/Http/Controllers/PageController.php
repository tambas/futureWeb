<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\World;

class PageController extends Controller
{
    public function download()
    {
        return view('pages.download');
    }

    public function servers()
    {
        $servers = config('dofus.details');
        foreach(config('dofus.servers') as $server)
        {
            $world = World::on($server.'_auth')->where('Name', strtoupper($server))->first();
            if (!$world || !$world->isOnline()) 
                $serverOnline[$server] = false;
            else
                $serverOnline[$server] = true;
        }
        
        return view('pages.servers', ['servers' => $servers, 'serverOnline' => $serverOnline]);
    }

    public function error_fake($code = 0)
    {
        return view('errors.fake', ['code' => $code]);
    }
}
