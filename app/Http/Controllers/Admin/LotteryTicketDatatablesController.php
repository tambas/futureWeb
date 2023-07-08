<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\LotteryTicket;
use App\Post;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Yajra\Datatables\Datatables;

class LotteryTicketDatatablesController extends Controller
{
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        $tickets = LotteryTicket::all();

        return Datatables::of($tickets)
            ->editColumn('user_id', function ($ticket) {
                $user = Cache::remember('lottery_tickets_user_'. $ticket->user_id, 5, function () use ($ticket) {
                    return User::where('Id', $ticket->user_id)->select('Email')->first();
                });
                if ($user) {
                    $text = '<a href="'.route('admin.user.edit', $ticket->user_id).'">'.$user->Email.'</a>';
                    return $text;
                } else {
                    return 'User not found ('.$ticket->user_id.')';
                }
            })
            ->editColumn('description', function ($ticket) {
                return '<img width="25" src="'.URL::asset($ticket->lottery()->icon_path).'">'.$ticket->description.'';
            })
            ->editColumn('used', function ($ticket) {
                return $ticket->used ? '<span class="label label-success">Yes - '.$ticket->updated_at->format("d/m/Y H:i:s").'</span>' : '<span class="label label-danger">No</span>';
            })
            ->editColumn('item_id', function ($ticket) {
                $jp = $ticket->max ? ' (JP)' : '';
                return $ticket->item($ticket->server) ? ''.$ticket->item($ticket->server)->name($ticket->server).''.$jp.'' : '';
            })
            ->editColumn('server', function ($ticket) {
                return $ticket->server ? ucfirst($ticket->server) : '';
            })
            ->editColumn('giver', function ($ticket) {
                return $ticket->giver();
            })
            ->make(true);
    }
}
