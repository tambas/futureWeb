<?php

namespace App\Http\Controllers;

use App\ForumAccountValidating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Exceptions\GenericException;
use App\Services\DofusForge;
use App\User;
use App\Account;
use App\ForumAccount;
use App\ForumKnownDevice;
use App\EmailModification;
use Redis;

use Mail;
use Validator;
use Auth;
use Cookie;
use \Cache;

use App\Helpers\EmailChecker;
use App\Mail\UserCreated;
use App\Mail\UserPasswordLost;
use App\Mail\UserPasswordChange;
use App\Mail\UserMail;
use App\Role;
use App\Character;
use App\World;
use App\MarketCharacter;

class AccountController extends Controller
{
    const SALT_LENGTH   = 8;
    const TICKET_LENGTH = 32;
    const TRANSACTIONS_PER_PAGE = 10;
    const VOTES_PER_PAGE = 10;

    public function register()
    {
        if (!Auth::guest()) {
            return redirect()->route('download');
        }
        return view('account/register');
    }

    public function store(Request $request)
    {
        $clientIp = \Illuminate\Support\Facades\Request::ip();

        $validator = Validator::make($request->all(), User::$rules['register']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $isEmailValid = EmailChecker::check($request->input('email'));

        if (!$isEmailValid) {
            return redirect()->back()->withErrors(['email' => "L'adresse email n'est pas valide."])->withInput();
        }

        $salt = str_random(self::SALT_LENGTH);

        $user = new User;
        $user->pseudo    = $request->input('pseudo');
        $user->email     = $request->input('email');
        $user->password  = $user->hashPassword($request->input('password'), $salt);
        $user->salt      = $salt;
        $user->firstname = $request->input('firstname');
        $user->lastname  = $request->input('lastname');
        $user->active    = false;
        $user->ticket    = str_random(self::TICKET_LENGTH);

        $user->save();

        $request->session()->put('password', $user->password);

        $forumAccount = new ForumAccount;
        $forumAccount->name              = $user->pseudo;
        $forumAccount->member_group_id   = config('dofus.forum.user_group');
        $forumAccount->email             = $user->email;
        $forumAccount->joined            = time();
        $forumAccount->ip_address        = '';
        $forumAccount->members_seo_name  = strtolower($user->pseudo);
        $forumAccount->members_pass_salt = $forumAccount->generateSalt();
        $forumAccount->members_pass_hash = $forumAccount->encryptedPassword($request->input('password'));
        $forumAccount->timezone          = 'Europe/Paris';
        $forumAccount->members_bitoptions = '1073807360';
        $forumAccount->save();

        $user->forum_id = $forumAccount->member_id;
        $user->save();

        $forumAccountValidating = new ForumAccountValidating;
        $forumAccountValidating->vid = $user->forum_id;
        $forumAccountValidating->member_id = $user->forum_id;
        $forumAccountValidating->new_reg = 1;
        $forumAccountValidating->ip_address = $clientIp;
        $forumAccountValidating->save();
		
	    $forumKnownDevice = new ForumKnownDevice;
        $forumKnownDevice->device_key = str_random(32);
        $forumKnownDevice->member_id = $user->forum_id;
        $forumKnownDevice->user_agent = $request->header('User-Agent');
        $forumKnownDevice->login_key = str_random(32);
        $forumKnownDevice->last_seen = 0;
        $forumKnownDevice->anonymous = 0;
        $forumKnownDevice->login_handler = "Internal";
        $forumKnownDevice->save();	

        setcookie('ips4_member_id', $forumAccount->member_id, 0, '/', config('dofus.forum.domain'));
        setcookie('ips4_login_key', $forumKnownDevice->login_key, 0, '/', config('dofus.forum.domain'));
        setcookie('ips4_device_key', $forumKnownDevice->device_key, 0, '/', config('dofus.forum.domain'));

        Mail::to($user)->send(new UserCreated($user));

        $request->session()->flash('notify', ['type' => 'info', 'message' => "Vous allez recevoir un email d'activation !"]);

        return redirect('/');
    }

    public function activation($ticket = null, Request $request)
    {
        $user = User::where('ticket', $ticket)->first();
        $clientIp = $request->ip();

        if (!$user) {
            $request->session()->flash('notify', ['type' => 'error', 'message' => "Clé d'activation invalide."]);
            return redirect('/');
        }

        Auth::login($user);

        $request->session()->put('password', $user->password);

        if ($user->active) {
            return redirect('/');
        }

        $user->ticket = str_random(self::TICKET_LENGTH);
        $user->active = true;
        $user->update([
            'ticket' => $user->ticket,
            'active' => $user->active,
            'last_ip_address' => $clientIp
        ]);

        $userForumValidating = ForumAccountValidating::where('vid', $user->forum_id)->first();
        if ($userForumValidating) {
            $userForumValidating->delete();
        }

        $forumAccount = $user->forum()->first();

        if ($forumAccount) {
            $forumAccount->members_bitoptions = '0';
            $forumAccount->save();
        }

        $request->session()->flash('notify', ['type' => 'success', 'message' => "Compte activé, bienvenue {$user->firstname} !"]);
        $request->session()->flash('popup', 'welcome');

        return redirect('/');
    }

    public function re_send_email(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user && !$user->active) {
            Mail::to($user)->send(new UserCreated($user));
            $request->session()->flash('notify', ['type' => 'info', 'message' => "Vous allez recevoir un nouvel email d'activation !"]);
        }

        return redirect('/');
    }

    public function password_lost()
    {
        return view('account/password-lost');
    }

    public function passord_lost_email(Request $request)
    {

        $validator = Validator::make($request->all(), User::$rules['password-lost-email']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            $user->ticket = str_random(self::TICKET_LENGTH);
            $user->update([
                'ticket' => $user->ticket
            ]);
            Mail::to($user)->send(new UserPasswordLost($user));
        }

        request()->session()->flash('notify', ['type' => 'info', 'message' => "Un email de réinitialisation de mot de passe a été envoyé."]);

        return redirect('/');
    }

    public function reset_form($ticket = null)
    {
        $user = User::where('ticket', $ticket)->first();

        if (!$user) {
            request()->session()->flash('notify', ['type' => 'error', 'message' => "Clé d'activation invalide."]);
            return redirect('/');
        }

        $user->active = true;
        $user->save();

        Auth::login($user);

        request()->session()->put('password', $user->password);
        request()->session()->flash('notify', ['type' => 'info', 'message' => "Veuillez changer votre mot de passe."]);

        return view('account/reset');
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), User::$rules['recover-password']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Auth::user()->salt     = str_random(self::SALT_LENGTH);
        Auth::user()->password = Auth::user()->hashPassword($request->input('password'), Auth::user()->salt);
        Auth::user()->ticket   = str_random(self::TICKET_LENGTH);
        Auth::user()->update([
            'password' => Auth::user()->password,
            'salt'     => Auth::user()->salt,
            'ticket'   => Auth::user()->ticket,
        ]);

        $request->session()->flash('notify', ['type' => 'success', 'message' => "Mot de passe mis à jour."]);

        return redirect()->route('profile');
    }

    public function profile()
    {
        $accounts = Auth::user()->accounts();
        return view('account/profile', compact('accounts'));
    }

    public function update(Request $request)
    {
        /*if ($request->input('firstname') && $request->input('lastname'))
        {
            $validator = Validator::make($request->all(), User::$rules['update-name']);

            if ($validator->fails())
            {
                return redirect()->back()->withErrors($validator)->withInput();
                //return $this->error(401, 'nom/prénom incorrect', $validator->errors()->all());
            }

            Auth::user()->firstname = $request->input('firstname');
            Auth::user()->lastname = $request->input('lastname');
            Auth::user()->update([
                'firstname' => Auth::user()->firstname,
                'lastname'  => Auth::user()->lastname,
            ]);
        }

        if ($request->input('password') && $request->input('passwordConfirmation'))
        {
            $validator = Validator::make($request->all(), User::$rules['update-password']);

            if ($validator->fails())
            {
                return redirect()->back()->withErrors($validator)->withInput();
                //return $this->error(401, 'mot de passe incorrect', $validator->errors()->all());
            }

            Auth::user()->salt     = str_random(self::SALT_LENGTH);
            Auth::user()->password = Auth::user()->hashPassword($request->input('password'), Auth::user()->salt);
            Auth::user()->update([
                'password' => Auth::user()->password,
                'salt'     => Auth::user()->salt,
            ]);
        }

        $request->session()->flash('notify', ['type' => 'success', 'message' => "Profile mis à jour."]);

        return redirect()->route('profile');
        //return $this->success('profile mis à jour');*/
    }

    public function change_email(Request $request)
    {
        $emailModification = EmailModification::where('user_id', 10)->first();

        if ($request->all()) {
            $rules = User::$rules['update-email'];
            $rules['passwordOld'] = str_replace('{PASSWORD}', Auth::user()->password, $rules['passwordOld']);
            $rules['passwordOld'] = str_replace('{SALT}', Auth::user()->salt, $rules['passwordOld']);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $emailModification = EmailModification::where('email_old', Auth::user()->email)->where('email_new', $request->input('email'))->orderBy('id', 'DESC')->first();

            if ($emailModification && ($emailModification->token_old != null || $emailModification->token_new != null)) {
                return view('account.valid-email', ['emailModification' => $emailModification]);
            }

            $isEmailValid = EmailChecker::check($request->input('email'));

            if (!$isEmailValid) {
               return redirect()->back()->withErrors(['email' => "L'adresse email n'est pas valide."])->withInput();
            }

            $emailModification = new EmailModification;
            $emailModification->user_id   = Auth::user()->id;
            $emailModification->token_old = str_random(self::TICKET_LENGTH);
            ;
            $emailModification->token_new = str_random(self::TICKET_LENGTH);
            ;
            $emailModification->email_old = Auth::user()->email;
            $emailModification->email_new = $request->input('email');
            $emailModification->save();

            $user = Auth::user();

            $datas = [];
            $datas[] = ['email' => $emailModification->email_old, 'token' => $emailModification->token_old, 'type' => 'old'];
            $datas[] = ['email' => $emailModification->email_new, 'token' => $emailModification->token_new, 'type' => 'new'];

            foreach ($datas as $data) {
                $email = $data['email'];
                Mail::to($email)->send(new UserMail($user, $data));
            }

            return view('account.valid-email', ['emailModification' => $emailModification]);
        }

        return view('account/change-email');
    }

    public function valid_email($type, $token)
    {
        $isEmailValidated = false;

        $token_type = '';

        if ($type == 'old') {
            $token_type = 'token_old';
        } elseif ($type == 'new') {
            $token_type = 'token_new';
        }

        if ($token_type == '') {
            throw new GenericException('email_token_invalid');
        }

        $emailModification = EmailModification::where($token_type, $token)->where('user_id', Auth::user()->id)->first();

        if (!$emailModification) {
            throw new GenericException('email_token_invalid');
        }

        if ($type == 'old') {
            $emailModification->token_old = null;
        } elseif ($type == 'new') {
            $emailModification->token_new = null;
        }

        $emailModification->save();

        if ($emailModification->token_old == null && $emailModification->token_new == null) {
            $isEmailValidated = true;
        }

        if ($isEmailValidated) {
            $email = $emailModification->email_new;

            $forumAccount = Auth::user()->forum()->first();

            if ($forumAccount) {
                $forumAccount->email = $email;
                $forumAccount->save();
            }

            Cache::forget('accounts_' . Auth::user()->id);

            $gameAccounts = Auth::user()->accounts();

            if ($gameAccounts) {
                foreach ($gameAccounts as $gameAccount) {
                    $gameAccount->Email = $email;
                    $gameAccount->save();
                }
            }

            Auth::user()->email = $email;
            Auth::user()->save();

            request()->session()->flash('notify', ['type' => 'success', 'message' => "Adresse email mise à jour."]);
            return redirect()->route('profile');
        }

        return view('account.valid-email', ['emailModification' => $emailModification]);
    }

    public function change_password(Request $request)
    {
        if ($request->all()) {
            $rules = User::$rules['update-password'];
            $rules['passwordOld'] = str_replace('{PASSWORD}', Auth::user()->password, $rules['passwordOld']);
            $rules['passwordOld'] = str_replace('{SALT}', Auth::user()->salt, $rules['passwordOld']);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Auth::user()->salt     = str_random(self::SALT_LENGTH);
            Auth::user()->password = Auth::user()->hashPassword($request->input('password'), Auth::user()->salt);
            Auth::user()->save();

            $request->session()->put('password', Auth::user()->password);

            $forumAccount = Auth::user()->forum()->first();

            if ($forumAccount) {
                $forumAccount->members_pass_salt = $forumAccount->generateSalt();
                $forumAccount->members_pass_hash = $forumAccount->encryptedPassword($request->input('password'));
                $forumAccount->save();
            }

            $user = Auth::user();

            Mail::to($user)->send(new UserPasswordChange($user));
            $request->session()->flash('notify', ['type' => 'success', 'message' => "Mot de passe mis à jour."]);

            return redirect()->route('profile');
        }

        return view('account/change-password');
    }

    public function change_profile(Request $request)
    {
        if ($request->all()) 
        {
            $rules = User::$rules['update-profile'];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $errorAvatar = ["avatar" => "Erreur avec l'avatar choisi"];

            if(count(explode('-', $request->avatar)) >= 2)
            {
                $requested_avatar['server'] = explode('-', $request->avatar)[1];
                $requested_avatar['characterid'] = explode('-', $request->avatar)[0];
            }
            else
            {
                return redirect()->back()->withErrors($errorAvatar)->withInput();
            }

            if (Auth::user()->avatar != config('dofus.default_avatar')) 
            {
                File::delete(Auth::user()->avatar);
            }

            if($request->avatar != "default-default")
            { 
                if (!World::isServerExist($requested_avatar['server'])) 
                    return redirect()->back()->withErrors($errorAvatar)->withInput();

                $character = Character::on($requested_avatar['server'] . '_world')->where('Id',  $requested_avatar['characterid'])->first();
                if(!$character)
                    return redirect()->back()->withErrors($errorAvatar)->withInput();

                $requested_avatar['account'] = $character->account($requested_avatar['server'])->Id;
                if(!$requested_avatar['account'])
                    return redirect()->back()->withErrors($errorAvatar)->withInput();

                if(!World::isCharacterOwnedByMe($requested_avatar['server'], $requested_avatar['account'], $requested_avatar['characterid']))
                    return redirect()->back()->withErrors($errorAvatar)->withInput();

                $link = DofusForge::player($character,$requested_avatar['server'], 'face', 1, 100, 100);
                $path = 'uploads/users/'.Auth::user()->id;
                $timestamp = Carbon::now()->timestamp;
                if(!File::exists($path))
                    File::makeDirectory($path);
                File::copy($link, 'uploads/users/'.Auth::user()->id.'/'.$timestamp.'-'.$requested_avatar['characterid'].'-'.$requested_avatar['server'].'.png');
                Auth::user()->avatar = 'uploads/users/'.Auth::user()->id.'/'.$timestamp.'-'.$requested_avatar['characterid'].'-'.$requested_avatar['server'].'.png';
            }
            else
            {
                Auth::user()->avatar = config('dofus.default_avatar');
            }
            if(!Auth::user()->isCertified())
            {
                Auth::user()->firstname = $request->input('firstname');
                Auth::user()->lastname  = $request->input('lastname');
            }
            Auth::user()->save();

            $request->session()->flash('notify', ['type' => 'success', 'message' => "Profil mis à jour."]);
            return redirect()->route('profile');
        }

        $authuser = Auth::user();
        $accounts = Auth::user()->accounts();
        $characters = [];
        
        foreach($accounts as $account)
        {
            $characters_db = $account->characters(false, false);
                foreach($characters_db as $character)
                {
                    array_push($characters, $character);
                }
        }
        return view('account/change-profile', compact('authuser', 'characters'));
    }

    public function certify(Request $request)
    {
        if ($request->all()) {
            $rules = User::$rules['certify'];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->birthday)) {
                return redirect()->back()->withErrors(['birthday' => 'Le format de la date de naissance est invalide (aaaa-mm-jj)'])->withInput();
            }

            $date = Carbon::parse($request->birthday);
            $year_max = Carbon::now()->year - config('dofus.certify.min_age');
            $year_min = Carbon::now()->year - config('dofus.certify.max_age');

            if ($date->year <= $year_max && $date->year >= $year_min) {
                Auth::user()->certified = true;
                Auth::user()->firstname = $request->input('firstname');
                Auth::user()->lastname  = $request->input('lastname');
                Auth::user()->birthday  = $request->input('birthday');
                Auth::user()->save();
            } else {
                return redirect()->back()->withErrors(['birthday' => 'Vous devez être âgé d\'au moins '.config('dofus.certify.min_age').' ans.'])->withInput();
            }

            // TODO: send email to user with explications

            $request->session()->flash('notify', ['type' => 'success', 'message' => "Votre compte est certifié!"]);
            return redirect()->route('profile');
        }

        if (!Auth::user()->certified) {
            $authuser = Auth::user();
            return view('account/certify', compact('authuser'));
        } else {
            abort(404);
        }
    }
    
    public function purchases(Request $request)
    {
        $authid = Auth::user()->id;
        $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;
        if (!is_numeric($page)) {
            abort(404);
        }
        
        $transactions = Cache::remember('account'.$authid.'_transactions_page_' . $page, 15, function () {
            return Auth::user()->transactions()->paginate(self::TRANSACTIONS_PER_PAGE);
        });

        return view ('account/purchases', compact('transactions'));
    } 

    public function votes(Request $request)
    {
        $authid = Auth::user()->id;
        $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;
        if (!is_numeric($page)) {
            abort(404);
        }
        
        $votes = Cache::remember('account'.$authid.'_votes_page_' . $page, 15, function () {
            return Auth::user()->votes()->paginate(self::VOTES_PER_PAGE);
        });

        return view ('account.votes', compact('votes'));
    } 

    public function market(Request $request)
    {
        $mcInSell = Auth::user()->marketCharacters()->inSell()->get();
        $mcSold =  Auth::user()->marketCharacters()->sold()->get();
        $mcBuyed = MarketCharacter::buyed(Auth::user()->id)->get();

        return view ('account.market', compact('mcInSell', 'mcSold', 'mcBuyed'));
    } 
}
