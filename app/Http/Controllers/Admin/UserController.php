<?php

namespace App\Http\Controllers\Admin;

use App\ForumAccountValidating;
use App\Http\Controllers\Controller;
use App\Lottery;
use App\LotteryTicket;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yuansir\Toastr\Facades\Toastr;
use App\ForumAccount;
use App\Mail\Admin\UserUpdated;
use App\Mail\UserCreated;
use App\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::getRoles();
        return view('admin.users.create', compact('roles'));
    }

    public function edit(User $user)
    {
        $user = User::findOrFail($user->id);
        $transactions = $user->transactions()->take(10)->get();
        $tickets = Cache::remember('tickets_admin_' . $user->id, 10, function () use ($user) {
            return LotteryTicket::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(10)->get();
        });
        $ticketsArray = Lottery::fetchTicketsType();
        $roles = Role::getRoles();

        return view('admin.users.edit', compact('user', 'transactions', 'tickets', 'ticketsArray', 'roles'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), User::$rules['admin-store']);

        if ($validator->fails()) {
            return redirect(route('admin.user.create'))
                ->withErrors($validator)
                ->withInput();
        }

        $salt = str_random(8);

        $user = new User;
        $user->pseudo    = $request->pseudo;
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->email     = $request->email;
        $user->role_id   = $request->role;
        $user->password  = $user->hashPassword($request->password, $salt);
        $user->salt      = $salt;

        $user->active = $request->active == 1 ? true : false;
        $user->ticket = $request->active == 1 ? null : str_random(32);
        $user->save();

        $forumAccount = new ForumAccount;
        $forumAccount->name              = $user->pseudo;
        $forumAccount->member_group_id   = config('dofus.forum.user_group');
        $forumAccount->email             = $user->email;
        $forumAccount->joined            = time();
        $forumAccount->ip_address        = '';
        $forumAccount->members_seo_name  = strtolower($user->pseudo);
        $forumAccount->members_pass_salt = $forumAccount->generateSalt();
        $forumAccount->members_pass_hash = $forumAccount->encryptedPassword($request->password);
        $forumAccount->timezone          = 'Europe/Paris';
        $forumAccount->save();

        $user->forum_id = $forumAccount->member_id;
        $user->save();

        if (!$request->active) {
            $forumAccountValidating = new ForumAccountValidating;
            $forumAccountValidating->vid = $user->forum_id;
            $forumAccountValidating->member_id = $user->forum_id;
            $forumAccountValidating->new_reg = 1;
            $forumAccountValidating->save();
        }

        if (!$request->active) {
            Mail::to($user)->send(new UserCreated($user));
            Toastr::success('E-mail send', $title = null, $options = []);
        }

        Toastr::success('User created', $title = null, $options = []);
        return redirect(route('admin.users'));
    }

    public function update(User $user, Request $request)
    {
        $rules = [
            'pseudo'    => 'required|min:3|max:32|alpha_dash|unique:users,pseudo,' . $user->id,
            'firstname' => 'required|min:3|max:32|alpha_dash',
            'lastname'  => 'required|min:3|max:32|alpha_dash',
            'birthday'  => 'nullable|date',
            'email'     => 'required|email|unique:users,email, ' . $user->id,
            'role'      => 'required|numeric|exists:roles,id',
            'points'    => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->pseudo    = $request['pseudo'];
        $user->firstname = $request['firstname'];
        $user->lastname  = $request['lastname'];
        $user->points    = $request['points'];
        $user->role_id   = $request['role'];
        $user->birthday  = is_null($request['birthday']) ? null : $request['birthday'];
        $user->save();
        
        $forumAccount = $user->forum()->first();

        if ($forumAccount) {
            $forumAccount->email = $request['email'];
            $forumAccount->name = $request['pseudo'];
            $forumAccount->members_seo_name  = strtolower($request['pseudo']);
            $forumAccount->save();
        }

        if ($request->useradvert == true) {
            Mail::to($user)->send(new UserUpdated($user));
            Toastr::success('E-mail send', $title = null, $options = []);
        }

        Cache::forget('accounts_' . $user->id);

        $gameAccounts = $user->accounts();

        if ($gameAccounts) {
            foreach ($gameAccounts as $gameAccount) {
                $gameAccount->Email = $request->input('email');
                $gameAccount->save();
            }
        }

        $user->email = $request['email'];
        $user->save();

        $servers = config('dofus.servers');
        if ($servers) {
            foreach ($servers as $server) {
                Cache::forget('accounts_' . $server . '_' . $user->id);
            }
        }

        Cache::forget('accounts_' . $user->id);

        Toastr::success('Account updated', $title = null, $options = []);

        return redirect()->back();
    }

    public function ban(User $user, Request $request)
    {
        $user = User::findOrFail($user->id);

        $user->banned = true;
        $user->banReason = $request->banReason;
        $user->save();

        return response()->json([], 200);
    }

    public function unban(User $user, Request $request)
    {
        $user = User::findOrFail($user->id);

        $user->banned = false;
        $user->banReason = null;
        $user->save();

        return response()->json([], 200);
    }

    public function activate(User $user, Request $request)
    {
        $user = User::findOrFail($user->id);

        $user->active = true;
        $user->ticket = null;
        $user->save();

        $userForumValidating = ForumAccountValidating::where('vid', $user->forum_id)->first();
        if ($userForumValidating) {
            $userForumValidating->delete();
        }

        $forumAccount = $user->forum()->first();

        if ($forumAccount) {
            $forumAccount->members_bitoptions = '0';
            $forumAccount->save();
        }

        return response()->json([], 200);
    }

    public function decertify(User $user, Request $request)
    {
        $user = User::findOrFail($user->id);

        $user->certified = false;
        $user->save();

        return response()->json([], 200);
    }

    public function certify(User $user, Request $request)
    {
        $validator = Validator::make($request->all(), User::$rules['certify']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $user = User::findOrFail($user->id);

        $user->firstname  = $request->firstname;
        $user->lastname   = $request->lastname;
        $user->birthday   = $request->birthday;
        $user->certified = true;
        $user->save();

        return response()->json([], 200);
    }

    public function password(User $user, Request $request)
    {
        $validator = Validator::make($request->all(), User::$rules['admin-update-password']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $user = User::findOrFail($user->id);

        $salt = str_random(8);

        $user->password  = $user->hashPassword($request->password, $salt);
        $user->salt      = $salt;
        $user->save();


        return response()->json([], 200);
    }

    public function resetAvatar(User $user, Request $request)
    {
        $user = User::findOrFail($user->id);
        $old_avatar = $user->avatar;
        if ($old_avatar != config('dofus.default_avatar')) {
            File::delete($old_avatar);
            $new_avatar = config('dofus.default_avatar');
            $user->avatar = $new_avatar;
            $user->save();

            return response()->json([], 200);
        } else {
            return response()->json([], 403);
        }
    }

    public function addTicket(User $user, Request $request)
    {
        $validator = Validator::make($request->all(), User::$rules['addticket']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $ticketsArray = Lottery::fetchTicketsType();
        if (!array_key_exists($request->ticket, $ticketsArray)) {
            return response()->json(['ticket' => ['0' => 'Ce type de ticket est invalide']], 400);
        }

        $ticket = new LotteryTicket;
        $ticket->type        = $request->ticket;
        $ticket->user_id     = $user->id;
        $ticket->description = $request->description;
        $ticket->giver       = Auth::user()->id;
        $ticket->save();

        Cache::forget('tickets_available_' . $user->id);
        Cache::forget('tickets_' . $user->id);
        Cache::forget('tickets_admin_' . $user->id);

        return response()->json([], 200);
    }

    public function re_send_email(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user && !$user->active) {
            Mail::to($user)->send(new UserCreated($user));
            Toastr::success('E-mail sended', $title = null, $options = []);
        }

        return redirect()->back();
    }
}
