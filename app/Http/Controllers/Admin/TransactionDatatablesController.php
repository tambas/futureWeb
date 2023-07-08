<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;

use App\Shop\ShopStatus;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Yajra\Datatables\Datatables;

class TransactionDatatablesController extends Controller
{
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        $transactions = Transaction::select(['id', 'user_id', 'provider', 'state', 'code', 'points', 'created_at'])->get();

        return Datatables::of($transactions)
            ->addColumn('action', function ($transaction) {
                $buttons =
                    '
                <a href="user/'.$transaction->id.'/edit" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>
                    ';

                return $buttons;
            })
            ->editColumn('state', function ($transaction) {
                $state = ShopStatus::getState($transaction->state);
                $type = 'info';
               
                if ($transaction->state == 0) {
                    $type = 'success';
                } elseif ($transaction->state == 1) {
                    $type = 'danger';
                } elseif ($transaction->state == 2) {
                        $type = 'danger';
                } elseif ($transaction->state == 3) {
                        $type = 'primary';
                } else {
                    $type = '';
                }

                return '<span class="label label-'.$type.'">'.ShopStatus::getState($transaction->state).'</span>';
            })
            ->editColumn('user_id', function ($transaction) {
                $user = Cache::remember('transaction_user_'. $transaction->user_id, 5, function () use ($transaction) {
                    return User::where('Id', $transaction->user_id)->select('Email')->first();
                });
                if ($user) {
                    $text = '<a href="'.route('admin.user.edit', $transaction->user_id).'">'.$user->Email.'</a>';
                    return $text;
                } else {
                    return 'User not found ('.$transaction->user_id.')';
                }
            })
            ->make(true);
    }
}
