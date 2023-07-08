<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Requests;
use Auth;
use Validator;
use \Cache;
use Carbon\Carbon;

use App\Vote;
use App\VoteReward;
use App\ItemTemplate;
use App\Gift;
use App\LotteryTicket;
use App\Services\DofusForge;
use App\User;
use App\VoteRequest;

class VoteController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guest()) {
            return view('vote.guest');
        }

        $palierId       = $this->palierId();
        $votesCount     = $this->userVotes();
        $giftsCount     = $this->giftsCount();
        $nextGifts      = $this->nextGift();
        $progress       = $this->progressBar($palierId);
        $steps          = $this->stepsList($palierId);
        $votesForTicket = $this->votesForTicket();
        $current        = (($votesCount + $nextGifts) / $this->votesForTicket()) % 5;

        /*$ip = \Illuminate\Support\Facades\Request::ip();
        $date = Carbon::now()->subHours(3)->toDateTimeString();
        $vote = Vote::where('ip', $ip)->where('created_at', '>=', $date)->orderBy('created_at', 'DESC')->first();

        if ($vote)
        {
            Auth::user()->last_vote = $vote->created_at;
            Auth::user()->save();
        }*/

        $this->checkVoteLimitByIP($request->ip());

        $delay = $this->delay();

        if ($current <= 0) {
            $current = 5;
        }

        $rpgId = 0;

        if (!config('dofus.rpg-paradize.use_callback')) {
            // 50/50 votes for each servers
            $rpgIndex = (int)file_get_contents(base_path() . "/vote");
            $rpgId = array_values(config('dofus.details'))[$rpgIndex]->rpg;
            file_put_contents(base_path() . "/vote", !$rpgIndex);
        }

        $data = [
            'palierId'       => $palierId,
            'votesCount'     => $votesCount,
            'giftsCount'     => $giftsCount,
            'nextGifts'      => $nextGifts,
            'progress'       => $progress,
            'steps'          => $steps,
            'votesForTicket' => $votesForTicket,
            'current'        => $current,
            'delay'          => $delay,
            'rpgId'          => $rpgId,
        ];

        if (Auth::user()->isFirstVote) {
            $data['popup'] = 'vote';
        }

        return view('vote.index', $data);
    }

    public function confirm()
    {
        $delay = $this->delay();

        if (!$delay->canVote) {
            return redirect()->route('vote.index');
        }

        return view('vote.confirm');
    }

    public function process(Request $request)
    {
        if (config('dofus.rpg-paradize.use_callback')) {
            return;
        }

        $this->checkVoteLimitByIP($request->ip());

        $delay = $this->delay();

        if (!$delay->canVote) {
            return redirect()->route('vote.index');
        }

        $rules = [
            'out'                  => 'required|integer',
            'g-recaptcha-response' => 'required|recaptcha'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $dateLastVote = date('Y-m-d H:i:s');
        $previousVote = Auth::user()->last_vote;

        // Store current date to avoid cheat (getOuts may be long)
        Auth::user()->last_vote = $dateLastVote;
        Auth::user()->save();

        $servers = config('dofus.details');
        $outVerified = false;

        foreach ($servers as $server) {
            $actualOUT = $this->getOuts($server->rpg);

            if (abs($actualOUT - $request->input('out')) <= 300) {
                $outVerified = true;
            }
        }

        if (!$outVerified) {
            // Bad OUT, restore preivous last vote date
            Auth::user()->last_vote = $previousVote;
            Auth::user()->save();

            return redirect()->back()->withErrors(['out' => 'Valeur OUT incorrect'])->withInput();
        }

        Auth::user()->votes  += 1;
        Auth::user()->jetons += 1;

        if (Auth::user()->isFirstVote) {
            Auth::user()->isFirstVote = false;
        }

        Auth::user()->save();

        $accounts = Auth::user()->accounts();

        foreach ($accounts as $account) {
            $account->LastVote = $dateLastVote;
            $account->save();
        }

        $vote = new Vote;
        $vote->user_id = Auth::user()->id;
        $vote->points  = 1; // jetons
        $vote->ip      = $request->ip();
        $vote->begin   = Carbon::now();
        $vote->end     = Carbon::now()->addHours(3);
        $vote->save();

        /*$usersWithSameIP = User::where('last_ip_address', $request->ip())->get();

        foreach ($usersWithSameIP as $user)
        {
            $user->last_vote = $dateLastVote;
            $user->save();

            $accounts = $user->accounts();

            foreach ($accounts as $account)
            {
                $account->LastVote = $dateLastVote;
                $account->save();
            }
        }*/

        Cache::forget('votes_' . Auth::user()->id);
        Cache::forget('votes_' . Auth::user()->id . '_10');

        if (Auth::user()->votes % $this->votesForTicket() == 0) {
            $ticket = new LotteryTicket;
            $ticket->type        = Auth::user()->votes % ($this->votesForTicket() * 5) == 0 ? LotteryTicket::GOLD : LotteryTicket::NORMAL;
            $ticket->user_id     = Auth::user()->id;
            $ticket->description = "Ticket " . Auth::user()->votes . " votes";
            $ticket->save();

            // If Nowel 2016
            $now   = Carbon::now();
            $begin = Carbon::create(2016, 12, 21);
            $end   = Carbon::create(2017, 01, 01);

            if ($now->gte($begin) && $now->lt($end)) {
                $ticket = new LotteryTicket;
                $ticket->type        = LotteryTicket::NOWEL;
                $ticket->user_id     = Auth::user()->id;
                $ticket->description = "Ticket de Nowel";
                $ticket->save();
            }

            Cache::forget('tickets_available_' . Auth::user()->id);
            Cache::forget('tickets_' . Auth::user()->id);

            $request->session()->flash('notify', ['type' => 'success', 'message' => "Vous avez reçu un nouveau ticket !"]);
            return redirect()->route('lottery.index');
        }

        $request->session()->flash('popup', 'ogrines');
        return redirect()->route('vote.index');
    }

    public function votesForTicket()
    {
        return config('dofus.votes_for_ticket');
    }

    public function palier($id = 1)
    {
        $votesCount = $this->userVotes();

        if ($id < 1 || $id > ceil(($votesCount+1) / ($this->votesForTicket() * 5))) {
            $id = 1;
        }

        $progress   = $this->progressBar($id);
        $steps      = $this->stepsList($id);
        $current    = 1;

        $data = [
            'palierId'       => $id,
            'votesForTicket' => $this->votesForTicket(),
            'progress'       => $progress,
            'steps'          => $steps,
            'current'        => $current,
        ];

        return view('vote.paliers', $data);
    }

    public function object($item = 1)
    {
        $json = [];

        if ($item % ($this->votesForTicket() * 5) == 0) {
            $json = [
                'name'        => 'Ticket de loterie doré',
                'description' => 'Ce ticket permet de jouer à la loterie premium.',
                'image'       => URL::asset('imgs/lottery/ticket_gold.png'),
            ];
        } else {
            $json = [
                'name'        => 'Ticket de loterie',
                'description' => 'Ce ticket permet de jouer à la loterie.',
                'image'       => URL::asset('imgs/lottery/ticket_normal.png'),
            ];
        }

        return json_encode($json);
    }

    private function userVotes()
    {
        return Auth::user()->votes;
    }

    private function palierId()
    {
        return intval($this->userVotes() / ($this->votesForTicket() * 5)) + 1;
    }

    private function giftsCount()
    {
        return intval($this->userVotes() / $this->votesForTicket());
    }

    private function nextGift()
    {
        return $this->votesForTicket() - ($this->userVotes() % $this->votesForTicket());
    }

    private function progressBar($palierId)
    {
        $progress = ($this->userVotes() - (($palierId - 1) * ($this->votesForTicket() * 5))) * 100 / ($this->votesForTicket() * 5);
        return $progress > 100 ? 100 : $progress;
    }

    private function stepsList($palierId)
    {
        return [
            1 => ($this->votesForTicket() * 5) * ($palierId - 1) + ($this->votesForTicket() * 1),
            2 => ($this->votesForTicket() * 5) * ($palierId - 1) + ($this->votesForTicket() * 2),
            3 => ($this->votesForTicket() * 5) * ($palierId - 1) + ($this->votesForTicket() * 3),
            4 => ($this->votesForTicket() * 5) * ($palierId - 1) + ($this->votesForTicket() * 4),
            5 => ($this->votesForTicket() * 5) * ($palierId - 1) + ($this->votesForTicket() * 5),
        ];
    }

    private function delay()
    {
        $obj = new \stdClass();

        $obj->now      = strtotime(date('Y-m-d H:i:s'));
        $obj->duration = $obj->now - strtotime(Auth::user()->last_vote);
        $obj->canVote  = $obj->duration < config('dofus.rpg-paradize.delay') ? false : true;
        $obj->wait     = config('dofus.rpg-paradize.delay') - $obj->duration;
        $obj->hours    = intval($obj->wait / 3600);
        $obj->minutes  = intval(($obj->wait % 3600) / 60);
        $obj->seconds  = intval((($obj->wait % 3600) % 60));

        return $obj;
    }

    private function getOuts($rpgId)
    {
        $outs = Cache::remember('rpg_outs_' . $rpgId, 1, function () use ($rpgId) {
            $curl = curl_init();

            $header[0] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,";
            $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 5";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3";

            curl_setopt($curl, CURLOPT_URL, 'http://www.rpg-paradize.com/site--' . $rpgId);
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:32.0) Gecko/20100101 Firefox/32.0");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_REFERER, 'http://www.rpg-paradize.com');
            curl_setopt($curl, CURLOPT_ENCODING, "gzip,deflate");
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_VERBOSE, false);
            curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookieRPG.txt');
            curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookieRPG.txt');

            $webpage  = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            preg_match('#Clic Sortant : ([0-9]+)#', $webpage, $matches);

            if (!isset($matches[0])) {
                return 0;
            }

            return substr($matches[0], 15);
        });

        return $outs;
    }

    private function checkVoteLimitByIP($ip)
    {
        $now = Carbon::now()->toDateTimeString();
        $max = Carbon::now()->subHours(3)->toDateTimeString();

        $votes = Vote::where('ip', $ip)->where(function ($query) use ($now, $max) {
            return $query->where('end', '>', $now)->where('begin', '>', $max);
        })->orderBy('id', 'DESC')->get();

        // Allow 3 votes by IP
        if (count($votes) >= 3) {
            $oldestVoteDate = Carbon::now();

            foreach ($votes as $vote) {
                $currentDate = Carbon::parse($vote->begin);

                if ($currentDate->lt($oldestVoteDate)) {
                    $oldestVoteDate = $currentDate;
                }
            }

            Auth::user()->last_vote = $oldestVoteDate->toDateTimeString();
            //Auth::user()->save();
        }
    }

    public function go()
    {
        $voteRequest = new VoteRequest;
        $voteRequest->user_id = Auth::user()->id;
        $voteRequest->token   = str_random(8);
        $voteRequest->save();

        // 50/50 votes for each servers
        $rpgIndex = (int)file_get_contents(base_path() . "/vote");
        $rpgId = array_values(config('dofus.details'))[$rpgIndex]->rpg;
        file_put_contents(base_path() . "/vote", !$rpgIndex);

        $url = 'http://www.rpg-paradize.com/?page=vote&vote=' . $rpgId . '&callback=' . URL::route('vote.callback', [$voteRequest->token]);

        return redirect($url);
    }

    public function callback(Request $request, $token = null)
    {
        if (!$token) {
            return "FAIL 1";
        }

        if (config('dofus.rpg-paradize.check_ip') && $request->ip() != config('dofus.rpg-paradize.ip')) {
            return "FAIL 5";
        }

        $voteRequest = VoteRequest::where('token', $token)->first();

        if (!$voteRequest) {
            return "FAIL 2";
        }

        $user = $voteRequest->user();

        if (!$user) {
            $voteRequest->delete();
            return "FAIL 3";
        }

        $now      = strtotime(date('Y-m-d H:i:s'));
        $duration = $now - strtotime($user->last_vote);
        $canVote  = $duration < config('dofus.rpg-paradize.delay') ? false : true;

        if (!$canVote) {
            $voteRequest->delete();
            return "FAIL 4";
        }

        $dateLastVote = date('Y-m-d H:i:s');

        $user->last_vote = $dateLastVote;
        $user->votes  += 1;
        $user->jetons += 1;

        if ($user->isFirstVote) {
            $user->isFirstVote = false;
        }

        $user->save();

        $accounts = $user->accounts();

        foreach ($accounts as $account) {
            $account->LastVote = $dateLastVote;
            $account->save();
        }

        $vote = new Vote;
        $vote->user_id = $user->id;
        $vote->points  = 1; // jetons
        $vote->ip      = $user->last_ip_address;
        $vote->begin   = Carbon::now();
        $vote->end     = Carbon::now()->addHours(3);
        $vote->save();

        Cache::forget('votes_' . $user->id);
        Cache::forget('votes_' . $user->id . '_10');

        if ($user->votes % $this->votesForTicket() == 0) {
            $ticket = new LotteryTicket;
            $ticket->type        = $user->votes % ($this->votesForTicket() * 5) == 0 ? LotteryTicket::GOLD : LotteryTicket::NORMAL;
            $ticket->user_id     = $user->id;
            $ticket->description = "Ticket " . $user->votes . " votes";
            $ticket->save();

            Cache::forget('tickets_available_' . $user->id);
            Cache::forget('tickets_' . $user->id);
        }

        $voteRequest->delete();

        return "OK";
    }
}
