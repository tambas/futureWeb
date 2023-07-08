<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Character;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;

use App\User;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class CharacterDatatablesController extends Controller
{
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($server)
    {
        $characters = Character::on($server.'_world')->select('Id', 'Name', 'Experience', 'Breed', 'DeletedDate')->get();
        foreach ($characters as $char) {
            $char->server = $server;
        }

        return Datatables::of($characters)
            ->editColumn('Breed', function ($character) {
                return $character->classe();
            })
            ->editColumn('Experience', function ($character) {
                return $character->level($character->server);
            })
            ->addColumn('Status', function ($character) {
                if ($character->isDeleted()) {
                    $pub = '<span class="label label-danger">Deleted</span> <span class="label label-danger">'.$character->DeletedDate->format('d-m-Y H:i:s').'</span>';
                } else {
                    $pub = '<span class="label label-success">Running</span>';
                }
                return $pub;
            })
            ->addColumn('GameAccount', function ($character) {
                $account = $character->account($character->server);
                if ($account && $account->user()) {
                    return ''.$account->Login.' ('.$account->Email.')<a href="'.route('admin.user.game.account.edit', [$account->user()->id, $character->server,$account->Id]).'" class="pull-right btn btn-xs btn-default" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>';
                }
                return '<span class="label label-danger">ERROR with this account</span>';
            })
            ->make(true);
    }
}
