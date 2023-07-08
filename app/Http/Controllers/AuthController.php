<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\User;
use App\ForumAccount;
use App\ForumAccountValidating;
use App\ForumKnownDevice;

use Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function auth(Request $request)
    {
        $clientIp = $request->ip();
        $user = User::where('email', $request->input('email'))->first();

        if ($user && ($user->password === $user->hashPassword($request->input('password'), $user->salt))) {
            if (!$user->active) {
                return view('account/re-send-email', ['user' => $user]);
            }
            if ($user->isBanned()) {
                $request->session()->flash('notify', ['type' => 'warning', 'message' => "Votre compte est banni."]);
                return redirect()->back()->withErrors(['auth' => 'Votre compte est banni.'])->withInput();
            }
            $user->last_ip_address = $clientIp;
            $user->save();

            if ($request->remember) {
                Auth::login($user, true);
            } else {
                Auth::login($user);
            }

            $request->session()->put('password', $user->password);

			if (!$user->forum_id)
			{
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
			}
			
			$forumKnownDevice = new ForumKnownDevice;
			$forumKnownDevice->device_key = str_random(32);
			$forumKnownDevice->member_id = $user->forum_id;
			$forumKnownDevice->user_agent = $request->header('User-Agent');
			$forumKnownDevice->login_key = str_random(32);
			$forumKnownDevice->last_seen = 0;
			$forumKnownDevice->anonymous = 0;
			$forumKnownDevice->login_handler = "Internal";
			$forumKnownDevice->save();
				
			setcookie('ips4_member_id', $forumKnownDevice->member_id, 0, '/', config('dofus.forum.domain'));
            setcookie('ips4_login_key', $forumKnownDevice->login_key, 0, '/', config('dofus.forum.domain'));
			setcookie('ips4_device_key', $forumKnownDevice->device_key, 0, '/', config('dofus.forum.domain'));

            $welcomeMessage = config('dofus.welcome.message');
            $welcomeMessage = preg_replace('/{user}/', $user->pseudo, $welcomeMessage);
            $request->session()->flash('notify', ['type' => 'info', 'message' => $welcomeMessage]);
            return redirect()->route('profile');
        } else {
            $request->session()->flash('notify', ['type' => 'error', 'message' => "Nom de compte ou mot de passe incorrect."]);
            return redirect()->back()->withErrors(['auth' => 'Nom de compte ou mot de passe incorrect.'])->withInput();
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            setcookie('ips4_member_id', '', time()-3600, '/', config('dofus.forum.domain'));
            setcookie('ips4_login_key', '', time()-3600, '/', config('dofus.forum.domain'));
            setcookie('ips4_device_key', '', time()-3600, '/', config('dofus.forum.domain'));
            setcookie('ips4_IPSSessionFront', '', time()-3600, '/', config('dofus.forum.domain'));

            Auth::logout();
        }
        return redirect('/');
    }
}
