<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Cache;
use \DB;
use App\World;
use App\Character;
use App\Guild;
use App\Exceptions\GenericException;
use App\Season;

class LadderController extends Controller
{
    const LADDER_CACHE_EXPIRE_MINUTES = 30;

    public function general($server)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $characters = Cache::remember('ladder.general.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server) {
            $db = config('database.connections');
            $auth  = $db[$server.'_auth']['database'];
            $world = $db[$server.'_world']['database'];

            $result = DB::table($world.'.characters AS ch')
                ->select('ch.Id', 'ch.Experience', 'ch.PrestigeRank', 'ch.Name', 'ch.Breed', 'ch.Sex')
                ->leftJoin($auth.'.worlds_characters AS wc', 'ch.Id', '=', 'wc.CharacterId')
                ->leftJoin($auth.'.accounts AS acc', 'wc.AccountId', '=', 'acc.Id')
                ->where('acc.UserGroupId', 1);
                                                    
            if (config('dofus.details')[$server]->prestige) {
                $result = $result->orderBy('ch.PrestigeRank', 'DESC');
            }

            $result = $result->orderBy('ch.Experience', 'DESC')
                ->take(100)
                ->get();

            return Character::hydrate($result->toArray());
        });

        return view('ladder.general', ['server' => $server, 'current' => 'general',  'characters' => $characters]);
    }

    public function pvp($server)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $characters = Cache::remember('ladder.pvp.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server) {
            $db = config('database.connections');
            $auth  = $db[$server.'_auth']['database'];
            $world = $db[$server.'_world']['database'];

            $result = DB::table($world.'.characters AS ch')
                ->select('ch.Id', 'ch.Name', 'ch.Breed', 'ch.Sex', 'ch.Honor', 'ch.AlignmentSide', 'ch.PrestigeRank', 'ch.Experience')
                ->leftJoin($auth.'.worlds_characters AS wc', 'ch.Id', '=', 'wc.CharacterId')
                ->leftJoin($auth.'.accounts AS acc', 'wc.AccountId', '=', 'acc.Id')
                ->where('acc.UserGroupId', 1)
                ->orderBy('ch.Honor', 'DESC')
                ->where('ch.Honor', '>', 0)
                ->where('ch.AlignmentSide', '>', 0)
                ->take(100)
                ->get();

            return Character::hydrate($result->toArray());
        });

        return view('ladder.pvp', ['server' => $server, 'current' => 'pvp', 'characters' => $characters]);
    }

    public function guild($server)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $guilds = Cache::remember('ladder.guilds.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server) {
            return Guild::on($server.'_world')->orderBy('Experience', 'DESC')->take(100)->get();
        });

        return view('ladder.guild', ['server' => $server, 'current' => 'guild',  'guilds' => $guilds]);
    }

    public function kolizeum($server)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $seasonActive = Season::where('active', 1)->where('server', $server)->first();

        $characters = Cache::remember('ladder.kolizeum.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server) {
            $db = config('database.connections');
            $auth  = $db[$server.'_auth']['database'];
            $world = $db[$server.'_world']['database'];

            $result = DB::table($world.'.characters AS ch')
                ->select('ch.Id', 'ch.Name', 'ch.Breed', 'ch.Sex', 'ch.ArenaRank', 'ch.Experience', 'ch.ArenaMatchsCount', 'ch.ArenaMatchsWon')
                ->leftJoin($auth.'.worlds_characters AS wc', 'ch.Id', '=', 'wc.CharacterId')
                ->leftJoin($auth.'.accounts AS acc', 'wc.AccountId', '=', 'acc.Id')
                ->where('acc.UserGroupId', 1)
                ->orderBy('ch.ArenaRank', 'DESC')
                ->where('ch.ArenaRank', '>', 0)
                ->take(100)
                ->get();

            return Character::hydrate($result->toArray());
        });

        return view('ladder.kolizeum', ['server' => $server, 'current' => 'kolizeum', 'characters' => $characters, 'seasonActive' => $seasonActive]);
    }

    public function kolizeum1v1($server)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }
        if(config('dofus.details')[$server]->version == '2.10')
        {
            throw new GenericException('invalid_server', $server);
        }

        $seasonActive = Season::where('active', 1)->where('server', $server)->first();

        $characters = Cache::remember('ladder.kolizeum1v1.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server) {
            $db = config('database.connections');
            $auth  = $db[$server.'_auth']['database'];
            $world = $db[$server.'_world']['database'];

            $result = DB::table($world.'.characters AS ch')
                ->select('ch.Id', 'ch.Name', 'ch.Breed', 'ch.Sex', 'ch.ArenaDuelRank', 'ch.Experience', 'ch.ArenaDuelMatchsCount', 'ch.ArenaDuelMatchsWon')
                ->leftJoin($auth.'.worlds_characters AS wc', 'ch.Id', '=', 'wc.CharacterId')
                ->leftJoin($auth.'.accounts AS acc', 'wc.AccountId', '=', 'acc.Id')
                ->where('acc.UserGroupId', 1)
                ->orderBy('ch.ArenaDuelRank', 'DESC')
                ->where('ch.ArenaDuelRank', '>', 0)
                ->take(100)
                ->get();

            return Character::hydrate($result->toArray());
        });

        return view('ladder.kolizeum1v1', ['server' => $server, 'current' => 'kolizeum', 'characters' => $characters, 'seasonActive' => $seasonActive]);
    }

    public function kolizeumSeasons(Request $request, $server)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        // Verifications d'anciennes saisons
        $oldSeasons = Season::where('active', 0)->where('server', $server)->whereNotNull('table')->get();

        if(!$oldSeasons || count($oldSeasons) == 0)
        {
            $request->session()->flash('notify', ['type' => 'info', 'message' => "Aucune ancienne saison trouvée"]);
            return redirect()->back();
        }

        $characters = [];

        foreach($oldSeasons as $season)
        {
            $characters[$season->id] = [];
            $result = Cache::remember('ladder.kolizeum.3first.season'.$season->id.'.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server, $season) {
                $result = DB::table($season->table)
                ->select('Id', 'Name', 'Breed', 'Sex', 'ArenaRank')
                ->orderBy('ArenaRank', 'DESC')
                ->where('ArenaRank', '>', 0)
                ->take(3)
                ->get();
    
                return Character::hydrate($result->toArray());
            });
            array_push($characters[$season->id], $result);
        }

        return view('ladder.seasons.kolizeum', ['server' => $server, 'oldSeasons' => $oldSeasons, 'characters' => $characters]);
    }
    
    public function kolizeum1v1Seasons(Request $request, $server)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        // Verifications d'anciennes saisons
        $oldSeasons = Season::where('active', 0)->where('server', $server)->whereNotNull('table')->get();

        if(!$oldSeasons || count($oldSeasons) == 0)
        {
            $request->session()->flash('notify', ['type' => 'info', 'message' => "Aucune ancienne saison trouvée"]);
            return redirect()->back();
        }

        $characters = [];

        foreach($oldSeasons as $season)
        {
            $characters[$season->id] = [];
            $result = Cache::remember('ladder.kolizeum1v1.3first.season'.$season->id.'.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server, $season) {
                $result = DB::table($season->table)
                ->select('Id', 'Name', 'Breed', 'Sex', 'ArenaDuelRank')
                ->orderBy('ArenaDuelRank', 'DESC')
                ->where('ArenaDuelRank', '>', 0)
                ->take(3)
                ->get();
    
                return Character::hydrate($result->toArray());
            });
            array_push($characters[$season->id], $result);
        }

        return view('ladder.seasons.kolizeum1v1', ['server' => $server, 'oldSeasons' => $oldSeasons, 'characters' => $characters]);
    }

    public function kolizeumSeason(Request $request, $server, $id)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $season = Season::where('active', 0)->where('id', $id)->where('server', $server)->first();

        if(!$season)
            abort(404);

        $seasons = Season::where('active', 0)->where('server', $server)->whereNotNull('table')->get();

        $characters = Cache::remember('ladder.kolizeum.season'.$season->id.'.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server, $season) {
            $result = DB::table($season->table)
                ->select('Id', 'Name', 'Breed', 'Sex', 'ArenaRank', 'Experience', 'ArenaMatchsCount', 'ArenaMatchsWon')
                ->orderBy('ArenaRank', 'DESC')
                ->where('ArenaRank', '>', 0)
                ->take(100)
                ->get();

            return Character::hydrate($result->toArray());
        });

        return view('ladder.seasons.view.kolizeum', ['server' => $server, 'characters' => $characters, 'season' => $season, 'seasons' => $seasons, 'current' => 'kolizeum']);
    }

    public function kolizeum1v1Season(Request $request, $server, $id)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $season = Season::where('active', 0)->where('id', $id)->where('server', $server)->first();

        if(!$season)
            abort(404);

        $seasons = Season::where('active', 0)->where('server', $server)->whereNotNull('table')->get();

        $characters = Cache::remember('ladder.kolizeum1v1.season'.$season->id.'.'.$server, self::LADDER_CACHE_EXPIRE_MINUTES, function () use ($server, $season) {
            $result = DB::table($season->table)
                ->select('Id', 'Name', 'Breed', 'Sex', 'ArenaDuelRank', 'Experience', 'ArenaDuelMatchsCount', 'ArenaDuelMatchsWon')
                ->orderBy('ArenaDuelRank', 'DESC')
                ->where('ArenaDuelRank', '>', 0)
                ->take(100)
                ->get();

            return Character::hydrate($result->toArray());
        });

        return view('ladder.seasons.view.kolizeum1v1', ['server' => $server, 'characters' => $characters, 'season' => $season, 'seasons' => $seasons, 'current' => 'kolizeum1v1']);
    }

}
