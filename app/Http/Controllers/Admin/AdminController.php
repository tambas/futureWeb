<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Http\Controllers\Controller;
use App\Post;
use App\Transaction;
use App\User;
use App\World;
use App\SupportRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yuansir\Toastr\Facades\Toastr;
use App\Role;

class AdminController extends Controller
{
    const CACHE_EXPIRE_MINUTES = 10;

    public function index()
    {
        $countuser = Cache::remember('countuser', self::CACHE_EXPIRE_MINUTES, function () {
            return User::count();
        });
        $countpost = Cache::remember('countpost', self::CACHE_EXPIRE_MINUTES, function () {
            return Post::count();
        });

        $countticket = Cache::remember('countticket', self::CACHE_EXPIRE_MINUTES, function () {
            return SupportRequest::count();
        });

        foreach (config('dofus.servers') as $server) {
            $countservers[$server] = Cache::remember('countservers_'.$server, self::CACHE_EXPIRE_MINUTES, function () use ($server) {
                return Account::on($server.'_auth')->count();
            });

            $world = World::on($server.'_auth')->where('Name', strtoupper($server))->first();
            if (!$world || !$world->isOnline()) {
                $connectedusers[$server] = null;
            } else {
                $connectedusers[$server] = $world->getOnlineCharacters();
            }
        }
        $todayearnings = Cache::remember('todayearnings', self::CACHE_EXPIRE_MINUTES, function () {
            return Transaction::GetDayEarnings(Carbon::today()->toDateString(), ',');
        });

        $count = ['users' => $countuser, 'posts' => $countpost, 'servers' => $countservers, 'connectedUsers' => $connectedusers, 'todayEarnings' => $todayearnings, 'tickets' => $countticket];

        $newusers = Cache::remember('newusers', self::CACHE_EXPIRE_MINUTES, function () {
            return  User::latest('created_at')->take(5)->select('id', 'pseudo', 'email', 'firstname', 'lastname', 'active', 'created_at')->get();
        });
        $newposts = Cache::remember('newposts', self::CACHE_EXPIRE_MINUTES, function () {
            return Post::latest('updated_at')->take(5)->select('id', 'title', 'type', 'author_id', 'published', 'updated_at')->get();
        });
        $newtickets = Cache::remember('newtickets', self::CACHE_EXPIRE_MINUTES, function () {
            return SupportRequest::latest('created_at')->take(5)->select('id', 'user_id', 'state', 'category', 'subject', 'message', 'assign_to', 'created_at')->get();
        });

        return view('admin.index', compact('newusers', 'count', 'newposts', 'newtickets'));
    }
}
