<?php

namespace App\Http\Controllers;

use App\Account;
use App\Guild;
use App\Exceptions\GenericException;
use App\RecoverCharacter;
use App\WorldCharacter;
use Illuminate\Http\Request;
use App\World;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\ItemPosition;
use App\Services\Stump;
use DB;
use Illuminate\Support\Facades\View;

class GuildController extends Controller
{
    const MEMBERS_PER_PAGE = 25;

    public function view(Request $request, $server, $guildId, $guildName)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $guild = Cache::remember('guild_view_'.$server.'_'.$guildId, 120, function () use($server, $guildId, $guildName) {
               return Guild::on($server . '_world')->where('Id', $guildId)->where('Name', $guildName)->first();
        });

        if(!$guild)
            abort(404);

        $guild->server = $server;
        $members = Cache::remember('guild_members_'.$server.'_'.$guildId, 120, function () use($server, $guild) {
               return $guild->members($server)->orderByRaw(DB::raw("RankId = 0, RankId"))->take(10)->get();
        });

        return view('guilds.view', compact('guild', 'server', 'members'));
    }

    public function members(Request $request, $server, $guildId, $guildName)
    {
        $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;

        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $guild = Cache::remember('guild_view_'.$server.'_'.$guildId, 120, function () use($server, $guildId, $guildName) {
               return Guild::on($server . '_world')->where('Id', $guildId)->where('Name', $guildName)->first();
        });

        if(!$guild)
            abort(404);

        $guild->server = $server;

        $members = Cache::remember('guild_members_'.$server.'_'.$guildId.'_page_'.$page, 120, function () use($server, $guild) {
               return $guild->members($server)->orderByRaw(DB::raw("RankId = 0, RankId"))->paginate(self::MEMBERS_PER_PAGE);
        });

        if ($request->pjax()) {
             return view('guilds.members', compact('guild', 'server', 'members'));
        }
        return view('guilds.members', compact('guild', 'server', 'members'));
    }
}
