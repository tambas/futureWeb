<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yuansir\Toastr\Facades\Toastr;
use App\World;

class CharacterController extends Controller
{
    public function index($server)
    {
        if (World::isServerExist($server)) {
            return view('admin.characters.index', compact('server'));
        } else {
            abort(404);
        }
    }
}
