<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Exceptions\GenericException;

use App\LotteryTicket;
use App\ItemTemplate;
use App\Gift;
use App\Services\DofusForge;
use Auth;
use \Cache;

class LotteryController extends Controller
{
    public function index()
    {
        return view('lottery.index');
    }

    public function servers($id)
    {
        return view('lottery.server', ['ticketId' => $id]);
    }

    public function draw($server, $id)
    {
        $ticket = LotteryTicket::find($id);

        if (!$ticket || $ticket->user_id != Auth::user()->id) {
            throw new GenericException('invalid_ticket');
        }

        if (count($ticket->objects($server)) <= 0) {
            throw new GenericException('ticket_no_objects');
        }

        return view('lottery.draw', ['server' => $server, 'ticket' => $ticket]);
    }

    public function process($server, $id)
    {
        $ticket = LotteryTicket::find($id);

        if (!$ticket || $ticket->user_id != Auth::user()->id || $ticket->used) {
            throw new GenericException('invalid_ticket');
        }

        if (count($ticket->objects($server)) <= 0) {
            throw new GenericException('ticket_no_objects');
        }

        $object = $ticket->draw($server);

        if ($object) {
            if (!$object->item($server)) {
                throw new GenericException('ticket_no_objects');
            }

            $gift = new Gift;
            $gift->user_id     = Auth::user()->id;
            $gift->item_id     = $object->item($server)->Id;
            $gift->description = $ticket->description;
            $gift->max         = $object->max;
            $gift->server      = $server;
            $gift->save();

            $ticket->item_id = $object->item($server)->Id;
            $ticket->max     = $object->max;
            $ticket->server  = $server;
            $ticket->used    = true;
            $ticket->save();

            Cache::forget('gifts_available_' . $server . '_' . Auth::user()->id);
            Cache::forget('gifts_' . Auth::user()->id);

            Cache::forget('tickets_available_' . Auth::user()->id);
            Cache::forget('tickets_' . Auth::user()->id);

            Cache::forget('tickets_admin_' . Auth::user()->id);

            return json_encode([
                'image'       => $object->item($server)->image(),
                'name'        => $object->item($server)->name($server) . ($object->max ? ' Jet Parfait' : ''),
                'description' => $object->item($server)->description($server),
            ]);
        }

        throw new GenericException('invalid_ticket');
    }
}
