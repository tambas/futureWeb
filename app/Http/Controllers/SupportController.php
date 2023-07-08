<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Exceptions\GenericException;
use Validator;
use Carbon\Carbon;
use App\Support\Support;
use App\ModelCustom;
use App\Account;
use App\Character;
use App\SupportRequest;
use App\SupportTicket;
use App\User;
use Auth;
use Mail;
use App\Mail\TicketOpen;
use App\World;
use Illuminate\Support\Facades\Cache;

class SupportController extends Controller
{
    private function isTicketRequestOwnedByMe($requestId)
    {
        return SupportRequest::where('id', $requestId)->where('user_id', Auth::user()->id)->first();
    }
    private function diffInMinutes($requestId, $type)
    {
        if(Auth::user()->isStaff())
            return "can";
        $supportRequest = SupportRequest::findOrFail($requestId);
        $mostRecent = $supportRequest->tickets()->select('updated_at')->where('user_id', $supportRequest->user_id)->where('reply', $type)->orderBy('id', 'desc')->first();
        if ($mostRecent) {
            $diffInMinutes = Carbon::now()->diffInMinutes($mostRecent->updated_at);
            if ($diffInMinutes < config('dofus.support.minutes_between_actions')) {
                return (int)(config('dofus.support.minutes_between_actions') - $diffInMinutes);
            } else {
                return "can";
            }
        }
        return "can";
    }

    private function diffInMinutesCreate()
    {
        if(Auth::user()->isStaff())
            return "can";
        $mostRecent = SupportRequest::select('created_at')->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        if ($mostRecent) {
            $diffInMinutes = Carbon::now()->diffInMinutes($mostRecent->created_at);
            if ($diffInMinutes < config('dofus.support.minutes_between_actions')) {
                return (int)(config('dofus.support.minutes_between_actions') - $diffInMinutes);
            } else {
                return "can";
            }
        }
        return "can";
    }

    public function index() // Open tickets
    {
        $requests = Auth::user()->supportRequests(SupportRequest::OPEN)->get();

        return view('support/index', compact('requests'));
    }

    public function closed() // Closed tickets
    {
        $requests = Auth::user()->supportRequests(SupportRequest::CLOSE)->get();

        return view('support/closed', compact('requests'));
    }

    public function show($requestId)
    {
        $request = SupportRequest::findOrFail($requestId);

        if (!$this->isTicketRequestOwnedByMe($requestId)) {
            throw new GenericException('not_ticket_owner');
        }

        $ticket = $request->ticket()->first(); // Initial ticket
        $messages = $request->tickets()->get(); // All messages (+ticket)

        $htmlReport = $request->generateHtmlReport(json_decode($ticket->data));

        foreach ($messages as $k => $message) { // Chaque ticket (messages)
            $infos = [];
            $datas = json_decode($message->data);

            foreach ($datas as $key => $data) {
                $keyData = explode('|', $key);
               
                if (count($keyData) < 2) {
                    continue;
                }

                $keyText = str_replace('"', ' ', $keyData[1]);
                if ($keyData[0] == 'message') {
                    $infos[$keyData[0]] = $data;
                }
            }
            $messages[$k]->data = $infos;
        }

        return view('support/show', compact('request', 'messages', 'htmlReport'));
    }

    public function create()
    {
        $html = Support::generateForm('support');
        return view('support/new', ['html' => $html]);
    }

    public function child(Request $request, $child, $params = false)
    {
        return Support::generateForm($child, $params, $request->all());
    }

    public function switchStatus(Request $request, $id)
    {
        if (!$this->isTicketRequestOwnedByMe($id)) {
            throw new GenericException('not_ticket_owner');
        }

        if ($this->diffInMinutes($id, 2) != 'can') {
            $minutes = $this->diffInMinutes($id, 2);
            $message = "Vous devez attendre encore $minutes minutes avant d'effectuer cette action.";
            $request->session()->flash('notify', ['type' => 'error', 'message' => $message]);
            return redirect()->back();
        }

        $supportRequest = SupportRequest::findOrFail($id);

        if ($supportRequest->isOpen()) {
            $supportRequest->state = SupportRequest::CLOSE;
            $supportRequest->save();
            $message = "Le ticket est maintenant cloturé";
        } else {
            $supportRequest->state = SupportRequest::OPEN;
            $supportRequest->save();
            $message = "Le ticket est maintenant ré-ouvert";
        }

        $newKey = 'message|Message';
        $messageArray[$newKey] = $message;

        $supportTicket = new SupportTicket;
        $supportTicket->request_id = $id;
        $supportTicket->user_id    = Auth::user()->id;
        $supportTicket->data       = json_encode($messageArray);
        $supportTicket->private    = false;
        $supportTicket->reply      = 2; // Info reply
        $supportTicket->save();

        Cache::forget('tickets_admin_open');
        Cache::forget('tickets_admin_close');
        Cache::forget('tickets_admin_mine');
        Cache::forget('newtickets');

        $request->session()->flash('notify', ['type' => 'success', 'message' => $message]);

        return redirect()->back();
    }

    public function postMessage(Request $request, $id)
    {

        if (!$this->isTicketRequestOwnedByMe($id)) {
            throw new GenericException('not_ticket_owner');
        }

        if ($this->diffInMinutes($id, 1) != 'can') {
            $minutes = $this->diffInMinutes($id, 1);
            $message = "Vous devez attendre encore $minutes minutes avant d'effectuer cette action.";
            $request->session()->flash('notify', ['type' => 'error', 'message' => $message]);
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), SupportTicket::$rules['postMessage']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $supportRequest = SupportRequest::findOrFail($id);
        $message = $request->input('message');
        if ($supportRequest->IsOpen()) {
            $newKey = 'message|Message';
            $messageArray[$newKey] = $message;

            $supportTicket = new SupportTicket;
            $supportTicket->request_id = $id;
            $supportTicket->user_id    = Auth::user()->id;
            $supportTicket->data       = json_encode($messageArray);
            $supportTicket->private    = false;
            $supportTicket->reply      = true;
            $supportTicket->save();

            $supportRequest->state = SupportRequest::OPEN;
            $supportRequest->save();
        }

        Cache::forget('tickets_admin_open');
        Cache::forget('tickets_admin_mine');
        Cache::forget('newtickets');

        $request->session()->flash('notify', ['type' => 'success', 'message' => 'Votre message a bien été envoyé']);
        return redirect()->back();
    }

    public function store(Request $request)
    {
        if ($this->diffInMinutesCreate() != 'can') {
            $minutes = $this->diffInMinutesCreate();
            $message = "Vous devez attendre encore $minutes minutes avant d'effectuer cette action.";
            return response()->json(['account' => [0 => $message]], 400);
        }

        $inputs = $request->all();
        $report = [];

        foreach ($inputs as $key => $value) {
            $keyData = explode('|', $key);

            if (count($keyData) < 2) {
                continue;
            }

            $keyType =  $keyData[0];
            $keyText =  $keyData[1];

            $keyTextFormated = str_replace('_', ' ', $keyText);
            $newKey = $keyType . '|' . $keyTextFormated;

            if ($keyType == 'message' || $keyType == 'text' || $keyType == 'email') {
                $report[$newKey] = $value;
                continue;
            }

            if ($keyType == 'image') {
                $report[$newKey] = $value;
                continue;
            }

            $valueData = explode('|', $value);

            if (count($valueData) < 2) {
                continue;
            }

            $valueType = $valueData[0];
            $valueText = $valueData[1];

            if ($keyType == 'account') {
                $report[$newKey] = (int)$valueText;
            }

            if ($keyType == 'character') {
                $report[$newKey] = (int)$valueText;
            }

            if ($keyType == 'server') {
                $server = $valueText;
                $report[$newKey] = $server;
            }

            if ($keyType == 'select') {
                $report[$newKey] = $valueText;
            }
        }


        if (array_key_exists('server|Serveur', $report)) {
            $server = $report['server|Serveur'];
            if (!World::isServerExist($server)) {
                return response()->json(['server' => [0 => "Ce serveur n'éxiste pas"]], 400);
            }
        }
        if (array_key_exists('account|Compte de jeu', $report)) {
            $accountId = $report['account|Compte de jeu'];
            $server = $report['server|Serveur'];
            $account = Account::on($server . '_auth')->where('Id', $accountId)->where('Email', Auth::user()->email)->first();
            if (!$account) {
                return response()->json(['account' => [0 => "Le compte de jeu n'éxiste pas"]], 400);
            }
        }
        if (array_key_exists('character|Personnage', $report)) {
            $accountId = $report['account|Compte de jeu'];
            $server = $report['server|Serveur'];
           
            $characterId = $report['character|Personnage'];

            if (!World::isCharacterOwnedByMe($server, $accountId, $characterId)) {
                return response()->json(['account' => [0 => "Le personnage n'éxiste pas"]], 400);
            }
        }
        
        $type = $report['select|Ma demande concerne'];

        $validator = Validator::make($report, SupportRequest::$rules[$type]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $new_array = array_filter(SupportRequest::$rules[$type], function ($val, $key) {
            return strpos($key, 'image') !== false;
        }, ARRAY_FILTER_USE_BOTH);
        
        if (!empty($new_array)) {
            foreach ($new_array as $k => $array) {
                if (array_key_exists($k, $report)) {
                    $image = $report[$k];
                    if (in_array($image->getClientOriginalExtension(), ['jpeg','jpg','png','bmp'])) {
                        $imageName = time() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path() . "/uploads/support", $imageName);
                        $report[$k] = $imageName;
                    }
                }
            }
        }

        $supportRequest = new SupportRequest;
        $supportRequest->user_id  = Auth::user()->id;
        $supportRequest->state    = SupportRequest::OPEN;
        $supportRequest->category = isset($report['select|Ma demande concerne']) ? $report['select|Ma demande concerne'] : "Sans catégorie";
        $supportRequest->subject  = isset($report['text|Sujet']) ? $report['text|Sujet'] : "Sans sujet";
        $supportRequest->message  = isset($report['message|Message']) ? $report['message|Message'] : "Pas de message";
        $supportRequest->save();

        $supportTicket = new SupportTicket;
        $supportTicket->request_id = $supportRequest->id;
        $supportTicket->user_id    = Auth::user()->id;
        $supportTicket->data       = json_encode($report);
        $supportTicket->private    = false;
        $supportTicket->reply      = false;
        $supportTicket->save();

        $user = Auth::user();

        Mail::to($user)->send(new TicketOpen($user, $supportRequest));
        Cache::forget('newtickets');
        Cache::forget('tickets_admin_open');
        return response()->json([Auth::user()->email], 200);
    }
}
