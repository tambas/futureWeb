<?php

namespace App\Http\Controllers\Admin;

use App\Announce;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\World;
use App\Role;
use Illuminate\Validation\Rule;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Kamaln7\Toastr\Facades\Toastr;
use App\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles =  Role::orderBy('id', 'asc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $id = Role::max('id');
        $id = (int)$id + 1;
        return view('admin.roles.create', compact('id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Role::$rules['store']);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // INSERT INTO DB //
        $role = new Role;
        if($request->id)
            $role->id = $request->id;
        $role->label = $request->name;
        $role->name  = strtolower($request->name);
        $role->save();

        Toastr::success('Role created', $title = null, $options = []);
        return redirect(route('admin.roles'));
    }

    public function destroy(Request $request, $roleid)
    {
        $role = Role::findOrFail($roleid);

        if($role->users()->count() > 0)
        {
            return response()->json(['roleid' => ['0' => 'Can\'t delete a role containing users']], 422);
        }

        $role->delete();
        $role->permissions()->detach();
        return response()->json([], 200);
    }

    public function edit($roleid)
    {
        $role = Role::findOrFail($roleid);
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, $roleid)
    {
        $role = Role::findOrFail($roleid);
        $validator = Validator::make($request->all(), [
                        'id' => [
                            'required',
                            Rule::unique('roles')->ignore($role->id, 'id'),
                        ],
                        'name'               => 'required|min:3|max:20|alpha',
                    ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $role->permissions()->newPivotStatement()->where('role_id', '=', $roleid)->update(array('role_id' => $request->id));
        $role->users()->update(['role_id' => $request->id]);
        $role->update(['id' => $request->id, 'label' => $request->name, 'name' => strtolower($request->name)]);

        Toastr::success('Role updated', $title = null, $options = []);

        return redirect(route('admin.roles'));
    }

    public function permissions($roleid)
    {
        $role = Role::findOrFail($roleid);
        $permissions = $role->permissions()->orderBy('id', 'asc')->get();
        
        $permissionsId = [];
        if($permissions)
        {
            foreach($permissions as $permission)
            {
                array_push($permissionsId, $permission->id);
            }
        }
        $permissionsDB = Permission::whereNotIn('id', $permissionsId)->orderBy('id', 'asc')->get();

        $permissionsdata = [];
        if ($permissionsDB) {
            foreach ($permissionsDB as $permission) {
                $permissionsdata[$permission->id] = $permission->label;
            }
        }
        return view('admin.roles.permissions', compact('role', 'permissions', 'permissionsdata'));
    }

    public function permissionRemove(Request $request, $roleid)
    {
        $validator = Validator::make($request->all(), Permission::$rules['attach&detach']);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        
        $role = Role::findOrFail($roleid);
        $role->permissions()->detach($request->permission);
        return response()->json([], 200);
    }

    public function permissionAdd(Request $request, $roleid)
    {
        $validator = Validator::make($request->all(), Permission::$rules['attach&detach']);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $role = Role::findOrFail($roleid);
        $permission = Permission::findOrFail($request->permission);
        $role->permissions()->attach($permission);

         return response()->json([], 200);
    }
}
