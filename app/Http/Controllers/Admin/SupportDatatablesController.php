<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\SupportRequest;
use App\Helpers\Utils;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\User;

class SupportDatatablesController extends Controller
{
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyDataOpen()
    {
        $tickets = Cache::remember('tickets_admin_open', 5, function () {
            return SupportRequest::where('state', '!=', SupportRequest::CLOSE)->get();
        });

        $dataTable = $this->generateDatatable($tickets);
        return $dataTable;
    }
    public function anyDataClosed()
    {
        $tickets = Cache::remember('tickets_admin_close', 5, function () {
            return SupportRequest::where('state', SupportRequest::CLOSE)->get();
        });

        $dataTable = $this->generateDatatable($tickets);
        return $dataTable;
    }

    public function anyDataMine()
    {
        $tickets = Cache::remember('tickets_admin_mine', 5, function () {
            return SupportRequest::where('state', '!=', SupportRequest::CLOSE)->where('assign_to', Auth::user()->id)->get();
        });

        $dataTable = $this->generateDatatable($tickets);
        return $dataTable;
    }

    private function generateDatatable($tickets)
    {
        return Datatables::of($tickets)
            ->editColumn('user_id', function ($ticket) {
                $user = Cache::remember('tickets_admin_user_'. $ticket->user_id, 20, function () use ($ticket) {
                    return User::where('Id', $ticket->user_id)->select('Pseudo')->first();
                });
                if ($user) {
                    $text = '<a href="'.route('admin.user.edit', $ticket->user_id).'">'.$user->Pseudo.'</a>';
                    return $text;
                } else {
                    return 'User not found ('.$ticket->user_id.')';
                }
            })
            ->editColumn('assign_to', function ($ticket) {
                if ($ticket->userAssigned()) {
                    return $ticket->userAssigned()->pseudo;
                }
                return "Not assigned";
            })
            ->editColumn('state', function ($ticket) {
                if ($ticket->state == SupportRequest::OPEN) {
                    $labeltype = "success";
                } elseif ($ticket->state == SupportRequest::WAIT) {
                     $labeltype = "primary";
                } else {
                    $labeltype = "danger";
                }

                return '<span class="label label-'.$labeltype.'">'.Utils::support_request_status($ticket->state, 1).'</span>';
            })
            ->addColumn('last_message', function ($ticket) {
                return $ticket->lastTicketAuthor() ? $ticket->lastTicketAuthor()->pseudo : "Error";
            })
            ->addColumn('action', function ($ticket) {
                if ($ticket->isOpen()) {
                    $switchButton = '<a data-id="'.$ticket->id.'" class="toswitch pull-right btn btn-xs btn-danger" data-toggle="tooltip" title="Close"><i class="fa fa-lock"></i></a>';
                } else {
                    $switchButton = '<a data-id="'.$ticket->id.'" class="toswitch pull-right btn btn-xs btn-primary" data-toggle="tooltip" title="Open"><i class="fa fa-unlock"></i></a>';
                }
                return '
                <a href="'.route('admin.support.ticket.show', $ticket->id).'" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a>
                '.$switchButton.'';
            })
            ->make(true);
    }
}
