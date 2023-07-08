<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Character;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use \Cache;
use App\World;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Kamaln7\Toastr\Facades\Toastr;

class GameAccountController extends Controller
{
    const SALT_LENGTH   = 8;
    const TICKET_LENGTH = 32;

    public function index(User $user, $server)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }
            $user = User::findOrFail($user->id);
            $accounts = $user->accounts($server);
            return view('admin.users.servers.index', compact('accounts', 'user', 'server'));
    }

    public function store(User $user, $server, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $database = $server . '_auth';

        $rules = Account::$rules['register'];
        $rules['login']    = str_replace('{DB}', $database, $rules['login']);
        $rules['nickname'] = str_replace('{DB}', $database, $rules['nickname']);

        $verifier = app()->make('validation.presence');
        $verifier->setConnection($database);
        $validator = Validator::make($request->all(), $rules);
        $validator->setPresenceVerifier($verifier);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $salt = str_random(self::SALT_LENGTH);

        $account = new Account;
        $account->changeConnection($database);
        $account->Login           = $request->login;
        $account->PasswordHash    = hash('sha512', $request->input('password') . '.' . $salt);
        $account->Salt            = $salt;
        $account->Nickname        = $request->nickname;
        $account->UserGroupId     = 1;
        $account->Ticket          = strtoupper(str_random(self::TICKET_LENGTH));
        $account->SecretQuestion  = 'Code secret disponible sur le site';
        $account->SecretAnswer    = sprintf('%04d', rand(0000, 9999));
        $account->Lang            = 'fr';
        $account->Email           = $user->email;
        $account->CreationDate    = date('Y-m-d H:i:s');
        $account->SubscriptionEnd = '2016-01-01 00:00:00';
        $account->IsJailed        = false;
        $account->IsBanned        = false;
        $account->server          = $request->server;
        $account->save();

        Cache::forget('accounts_' . $server . '_' . $user->id);
        Cache::forget('accounts_' . $user->id);

        return response()->json([], 202);
    }

    public function edit(User $user, $server, $accountId)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $user = User::findOrFail($user->id);
        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();
        $account->server = $server;
        $characters = $account->characters(true);
        return view('admin.users.servers.edit', compact('user', 'server', 'account', 'characters'));
    }

    public function update(User $user, $server, $accountId, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }
        $database = $server . '_auth';
        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();
        $this->validate($request, [
            'Login'                => 'required|min:3|max:32|unique:'.$database.'.accounts,Login,'.$account->Id.'|alpha_dash',
            'UserGroupId'          => 'required|numeric',
        ]);

        if (!array_key_exists($request->UserGroupId, config('dofus.ranks'))) {
            return redirect()->back();
        }
        $account->Login = $request->Login;
        $account->UserGroupId = $request->UserGroupId;
        $account->save();

        Cache::forget('accounts_' . $account->user()->id);

        Toastr::success('Game account updated');
        return redirect()->route('admin.user.game.accounts', [$user->id, $server]);
    }
    public function password(User $user, $server, $accountId, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }
        $validator = Validator::make($request->all(), Account::$rules['admin-update-password']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $salt = str_random(self::SALT_LENGTH);

        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();
        $account->PasswordHash    = hash('sha512', $request->input('password') . '.' . $salt);
        $account->Salt            = $salt;
        $account->save();

        return response()->json([], 202);
    }

    public function ban(User $user, $server, $accountId, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $validator = Validator::make($request->all(), Account::$rules['sanction']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $bannerUser = Auth::user();
        $bannerAccount = Account::on($server . '_auth')->where('Email', $bannerUser->email)->first();
        $bannerAccountId = $bannerAccount ? $bannerAccount->Id : 0;

        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();

        if ($request->allaccounts == '0') {
            $account->BanReason = $request->BanReason;
            $account->BanEndDate = $request->life == '1' ? null : $request->BanEndDate;
            $account->IsBanned = true;
            $account->BannerAccountId = $bannerAccountId;
            $account->save();
        }
        if ($request->allaccounts == '1') {
            $accounts = Account::on($server . '_auth')->where('Email', $account->Email)->get();

            foreach ($accounts as $account) {
                $account->BanReason = $request->BanReason;
                $account->BanEndDate = $request->life == '1' ? null : $request->BanEndDate;
                $account->IsBanned = true;
                $account->BannerAccountId = $bannerAccountId;
                $account->save();
            }
        }

        Cache::forget('accounts_' . $server . '_' . $account->user()->id);

        return response()->json([], 202);
    }

    public function unban(User $user, $server, $accountId, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();

        $account->IsBanned = false;
        $account->BanEndDate = null;
        $account->save();

        Cache::forget('accounts_' . $server . '_' . $account->user()->id);

        return response()->json([], 202);
    }

    public function jail(User $user, $server, $accountId, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $validator = Validator::make($request->all(), Account::$rules['sanction']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $bannerUser = Auth::user();
        $bannerAccount = Account::on($server . '_auth')->where('Email', $bannerUser->email)->first();
        $bannerAccountId = $bannerAccount ? $bannerAccount->Id : 0;

        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();

        if ($request->allaccounts == '0') {
            $account->BanReason = $request->BanReason;
            $account->BanEndDate = $request->life == '1' ? null : $request->BanEndDate;
            $account->IsJailed = true;
            $account->BannerAccountId = $bannerAccountId;
            $account->save();
        }

        if ($request->allaccounts == '1') {
            $accounts = Account::on($server . '_auth')->where('Email', $account->Email)->get();

            foreach ($accounts as $account) {
                $account->BanReason = $request->BanReason;
                $account->BanEndDate = $request->life == '1' ? null : $request->BanEndDate;
                $account->IsJailed = true;
                $account->BannerAccountId = $bannerAccountId;
                $account->save();
            }
        }

        Cache::forget('accounts_' . $server . '_' . $account->user()->id);
    }

    public function unjail(User $user, $server, $accountId, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();

        $account->IsJailed = false;
        $account->BanEndDate = null;
        $account->save();

        Cache::forget('accounts_' . $server . '_' . $account->user()->id);

        return response()->json([], 202);
    }
}
