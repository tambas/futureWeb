<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Http\Controllers\Controller;

use App\Post;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yuansir\Toastr\Facades\Toastr;

class SettingsController extends Controller
{
    public function index()
    {
        $motd    = config('dofus.motd');
        $theme   = config('dofus.theme');
        $welcome = config('dofus.welcome');

        $posts_id_object = Post::select('id', 'title')->get();
        $posts = [];

        if ($posts_id_object) {
            foreach ($posts_id_object as $post_id_object) {
                $posts[$post_id_object->id] = "{$post_id_object->title} (id: {$post_id_object->id})";
            }
        }

        return view('admin.settings.index', compact('motd', 'posts', 'theme', 'welcome'));
    }

    public function update(Request $request)
    {
        $json = json_decode(@file_get_contents("../settings.json"));

        if ($request->settings_type == 'motd')
         {
            if (@!isset($json->motd)) 
            {
                $json->motd = new \stdClass;
            }
        // TODO: Validation
            $json->motd->title    = $request->title;
            $json->motd->subtitle = $request->subtitle;
            $json->motd->post_id  = $request->post_id;

            Toastr::success('Motd updated', $title = null, $options = []);
        }

        if ($request->settings_type == 'theme') 
        {
            if (@!isset($json->theme)) {
                $json->theme = new \stdClass;
            }
        // TODO: Validation
            $json->theme->background = $request->background;
            $json->theme->color      = $request->color;
            $json->theme->animated   = $request->animated ? true : false;

            Toastr::success('Theme updated', $title = null, $options = []);
        }

        if ($request->settings_type == 'welcome') 
        {
            if (@!isset($json->welcome)) {
                $json->welcome = new \stdClass;
            }
        // TODO: Validation
            $json->welcome->message = $request->message;

            Toastr::success('Welcome updated', $title = null, $options = []);
        }
        file_put_contents("../settings.json", json_encode($json, JSON_UNESCAPED_UNICODE));

        return redirect(route('admin.settings'));
    }
}
