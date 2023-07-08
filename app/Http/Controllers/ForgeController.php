<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redis;

use App\Character;
use App\Services\DofusForge;

class ForgeController extends Controller
{
    public function image($request)
    {
        print DofusForge::image($request);
    }

    public function player($id, $mode, $orientation, $sizeX, $sizeY)
    {
        print DofusForge::player($id, $mode, $orientation, $sizeX, $sizeY);
    }

    public function text($id)
    {
        print DofusForge::text($id);
    }
}
