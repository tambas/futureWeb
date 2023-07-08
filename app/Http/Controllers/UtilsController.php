<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\World;
use App\Character;

class UtilsController extends Controller
{
    public function checkNameAvailability(Request $request, $server, $name)
    {
        if (!World::isServerExist($server))
            abort(404);
        if(!preg_match('/^[A-Z][a-z]{2,9}(?:-[A-Za-z][a-z]{2,9}|[a-z]{1,10})$/', $name))
            abort(404);
        $character = Character::on($server . '_world')->where('Name', $name)->where('DeletedDate', null)->first();
        if ($character)
            return response()->json(['error' => 'Ce pseudo est déjà utilisé'], 200);
        else
            return response()->json(['success' => 'Le pseudo est libre'], 200);
    }
}
