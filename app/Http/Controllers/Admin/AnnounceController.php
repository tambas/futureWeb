<?php

namespace App\Http\Controllers\Admin;

use App\Announce;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\World;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Kamaln7\Toastr\Facades\Toastr;

class AnnounceController extends Controller
{
    public function index($server)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $announces =  Announce::on($server.'_world')->get();
        return view('admin.announces.index', compact('announces', 'server'));
    }

    public function create($server)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        return view('admin.announces.create', compact('server'));
    }

    public function store($server, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $validator = Validator::make($request->all(), Announce::$rules['store&update']);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $database = $server.'_world';

        // INSERT INTO DB //
        $announce = new Announce;
        $announce->changeConnection($database);
        $announce->Message = $request->Message;
        $announce->Color = '-65536';
        $announce->save();

        Toastr::success('Announce created', $title = null, $options = []);
        return redirect(route('admin.announces', $server));
    }

    public function destroy($server, Announce $announce, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }
        $this->authorize('destroy', $announce); // Edit later (return true)

        $announce = Announce::on($server.'_world')->findOrFail($request->Id);

        $announce->delete();

        return response()->json([], 200);
    }

    public function edit($server, $id)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $announce = Announce::on($server.'_world')->findOrFail($id);
        return view('admin.announces.edit', compact('announce', 'server'));
    }

    public function update($server, $id, Request $request)
    {
        if (!World::isServerExist($server)) {
            abort(404);
        }

        $validator = Validator::make($request->all(), Announce::$rules['store&update']);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $announce = Announce::on($server.'_world')->findOrFail($id);
        $announce->update(['Message' => $request->Message]);

        return redirect(route('admin.announces', $server));
    }
}
