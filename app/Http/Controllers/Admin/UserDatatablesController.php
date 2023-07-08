<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;

use App\User;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class UserDatatablesController extends Controller
{
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        $users = User::select(['id', 'pseudo', 'email', 'firstname', 'lastname', 'birthday','role_id', 'certified', 'active', 'points', 'votes','banned', 'banReason'])->get();

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                $buttons;
                $buttons =
                    '
                <a href="user/'.$user->id.'/edit" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>
                    ';

                if ($user->isBanned()) {
                    $buttons .= '<a id="unban-'.$user->id.'" class="unban pull-right btn btn-xs btn-info" data-toggle="tooltip" title="Unban"><i class="fa fa-check-circle-o"></i></a>';
                } else {
                    $buttons .= '<a id="ban-'.$user->id.'" class="ban pull-right btn btn-xs btn-danger" data-toggle="tooltip" title="Ban"><i class="fa fa-ban"></i></a>';
                }
                if (!$user->isActive()) {
                    $buttons .= '<a id="active-'.$user->id.'" class="activ btn btn-xs btn-warning" data-toggle="tooltip" title="Confirm"><i class="fa fa-check"></i></a>';
                }
                return $buttons;
            })
            ->editColumn('pseudo', function ($user) {
                $ac = '<a href="user/'.$user->id.'/edit">'.$user->pseudo.'</a>';
                return $ac;
            })
            ->editColumn('role', function ($user) {
                if($user->role)
                    return $user->role->label;
                else
                    return 'Role not found';
            })
            ->editColumn('certified', function ($user) {
                if ($user->isCertified()) {
                    $ac = '<span class="label label-success"><i class="fa fa-lock"></i></span>';
                } else {
                    $ac = '<span class="label label-danger text-center"><i class="fa fa-unlock"></i></span>';
                }
                return $ac;
            })
            ->editColumn('active', function ($user) {
                if ($user->isActive()) {
                    $ac = '<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
                } else {
                    $ac = '<span class="label label-danger"><i class="fa fa-minus-circle"></i></span>';
                }
                return $ac;
            })
            ->make(true);
    }
}
