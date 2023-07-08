<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yuansir\Toastr\Facades\Toastr;
use App\User;
use App\Services\DofusForge;
use App\Character;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.profile');
    }

    public function accountUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), User::$rules['admin-update-profile']);

        if ($validator->fails()) {
            return redirect(route('admin.account'))
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];

        if ($request->hasFile('avatar')) {
            $old_avatar = Auth::user()->avatar;
            $user_id = Auth::user()->id;
            $file = $request->file('avatar');

            $image_name = "avatar-".$file->getClientOriginalName();

            if ($old_avatar != config('dofus.default_avatar')) {
                File::delete($old_avatar);
            }

            $file->move('uploads/users/'.$user_id, $image_name);

            Image::make(sprintf('uploads/users/'.$user_id.'/%s', $image_name))->resize(200, 200)->save();
            $user->avatar = 'uploads/users/'.$user_id.'/'.$image_name;
        }
        Toastr::success('Account updated', $title = null, $options = []);
        $user->save();

        return redirect()->route('admin.account');
    }

    public function resetAvatar()
    {
        $user = Auth::user();
        if (Auth::user()->avatar != config('dofus.default_avatar')) {
            $old_avatar = $user->avatar;
            File::delete($old_avatar);
            $new_avatar = config('dofus.default_avatar');
            $user->avatar = $new_avatar;
            $user->save();

            return response()->json([], 200);
        } else {
            return response()->json([], 403);
        }
    }

    public function password()
    {
        return view('admin.password');
    }

    public function passwordUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), User::$rules['admin-update-password']);

        if ($validator->fails()) {
            return redirect(route('admin.password'))
                ->withErrors($validator)
                ->withInput();
        }

        Auth::user()->salt     = str_random(8);
        Auth::user()->password = Auth::user()->hashPassword($request->input('password'), Auth::user()->salt);
        Auth::user()->update([
            'password' => Auth::user()->password,
            'salt'     => Auth::user()->salt,
        ]);

        Toastr::success('Password updated', $title = null, $options = []);
        return redirect()->route('admin.account');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function settingsUpdate(Request $request)
    {
        if ($request->settings_type == 'preloadtext')
         {
            $json = json_decode(Auth::user()->settings);

            if(!$json)
                $json = new \stdClass;

            if (@!isset($json->preloadtext)) 
                $json->preloadtext = new \stdClass;
 
            $json->preloadtext   = $request->preloadtext;

            Toastr::success('Preloadtext updated', $title = null, $options = []);
        }

        Auth::user()->settings = json_encode($json);
        Auth::user()->save();

        return redirect(route('admin.account.settings'));
    }

    public function templateAdd(Request $request)
    {
        $rules = [
            'title' => 'required|alpha_dash|between:1,20',
            'description' => 'required|between:1,30',
            'content' => 'required|between:10,5000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $json = json_decode(Auth::user()->settings);

        if(!$json)
            $json = new \stdClass;
        if(@!isset($json->templates))
            $json->templates = [];

        // VALIDATION
        $json2 = json_decode(Auth::user()->settings);
        if($json2 && @isset($json2->templates))
        {
            $collect = collect($json2->templates);
            $check = $collect->where('title', $request->title)->first();
            if($check)
                return response()->json(['title' => ['0' => 'Ce titre éxiste déjà']], 400);  
        }

        // CREATION
        $new = new \stdClass;
        $new->title = $request->title;
        $new->description = $request->description;
        $new->content = $request->content;

        array_push($json->templates, $new);

        Auth::user()->settings = json_encode($json);
        Auth::user()->save();

        return response()->json([], 200);
    }
    public function templateEdit(Request $request, $templateTitle)
    {
        $json = json_decode(Auth::user()->settings);

       if(!$json)
            return redirect(route('admin.account.settings'));
       if(@!isset($json->templates))
            return redirect(route('admin.account.settings'));
        
        $collect = collect($json->templates);
        $template = $collect->where('title', $templateTitle)->first();
        if(@!isset($template))
            return redirect(route('admin.account.settings'));

         return view('admin.templates.edit', compact('template'));
    }
    public function templateUpdate(Request $request, $templateTitle)
    {
        $rules = [
            'title' => 'required|alpha_dash|between:1,20',
            'description' => 'required|between:1,30',
            'content' => 'required|between:10,5000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $json = json_decode(Auth::user()->settings);

        if(!$json)
            return redirect(route('admin.account.settings'));
        if(@!isset($json->templates))
            return redirect(route('admin.account.settings'));

        $collect = collect($json->templates);
        $templateToUpdate = $collect->where('title', $templateTitle)->first();

        if(@!isset($templateToUpdate))
            return redirect(route('admin.account.settings'));
    
        // VALIDATION
        if($templateTitle != $request->title)
        {
            $json2 = json_decode(Auth::user()->settings);
            if($json2 && @isset($json2->templates))
            {
                $collect = collect($json2->templates);
                $check = $collect->where('title', $request->title)->first();
                if($check)
                {
                    return redirect()->back()
                    ->withErrors(['title' => 'Ce titre éxiste déjà'])
                    ->withInput();
                }
            }
        }

        $templateToUpdate->title = $request->title;
        $templateToUpdate->description = $request->description;
        $templateToUpdate->content = $request->content;

        Auth::user()->settings = json_encode($json);
        Auth::user()->save();

        Toastr::success('Template updated', $title = null, $options = []);
        return redirect(route('admin.account.settings'));
    }
    public function templateDestroy(Request $request, $templateTitle)
    {
        $json = json_decode(Auth::user()->settings);

        if(!$json)
            return response()->json([], 404);
        if(@!isset($json->templates))
            return response()->json([], 404);

        $collect = collect($json->templates);

        $filtered = $collect->reject(function ($value, $key) use($templateTitle) {
            if($value->title == $templateTitle)
                return true;
        });
        $json->templates = $filtered->toArray();

        Auth::user()->settings = json_encode($json);
        Auth::user()->save();
        
        return response()->json([], 200);
    }
}
