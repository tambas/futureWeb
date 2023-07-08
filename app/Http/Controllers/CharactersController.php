<?php

namespace App\Http\Controllers;

use App\Account;
use App\Character;
use Carbon\Carbon;
use App\Exceptions\GenericException;
use App\RecoverCharacter;
use App\WorldCharacter;
use Illuminate\Http\Request;
use App\World;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\ItemPosition;
use App\Services\Stump;
use App\MarketCharacter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
class CharactersController extends Controller
{
    public function view(Request $request, $server, $characterId, $characterName)
    {
        $character = $this->urlValidationAndGetCharacter($server,$characterId,$characterName);
        $character->server = $server;
        $marketCharacter = null;
        if(MarketCharacter::inSell($character))
            $marketCharacter = MarketCharacter::where('character_id', $character->Id)->where('buy_date', null)->first();

        $user = $character->user();
        if(!$user)
             abort(404);
        $settings = $user->getCharactersSettings($server,$characterId);

        return view('gameaccount.character.view', compact('character', 'server', 'marketCharacter', 'settings'));
    }

    public function caracteristics(Request $request, $server, $characterId, $characterName)
    {
         $character = $this->urlValidationAndGetCharacter($server,$characterId,$characterName);
        $character->server = $server;
        $marketCharacter = null;
        if(MarketCharacter::inSell($character))
            $marketCharacter = MarketCharacter::where('character_id', $character->Id)->where('buy_date', null)->first();
        
        $user = $character->user();
        if(!$user)
             abort(404);
        $settings = $user->getCharactersSettings($server,$characterId);

        $json = $character->getJsonInventoryEquiped();

        if(is_null($json))
        {
          $request->session()->flash('notify', ['type' => 'warning', 'message' => "Affichage impossible actuellement"]);
          return redirect()->back();
        }

        $itemsall = $character->getEquipment($json);
        $itemsleft = collect($itemsall['left']);
        $itemsright = collect($itemsall['right']);
        $itemsbottom = collect($itemsall['bottom']);
        $costume = collect($itemsall['costume']);

        $spells = Cache::remember('character_spells'.$server.'_'.$characterId, 30, function () use($character){
            return $character->spells()->get();
        });

        return view('gameaccount.character.caracteristics', compact('json', 'character', 'server', 'spells', 'itemsleft', 'itemsright', 'itemsbottom', 'costume', 'marketCharacter', 'settings'));
    }
    
    public function inventory(Request $request, $server, $characterId, $characterName)
    {
        $character = $this->urlValidationAndGetCharacter($server,$characterId,$characterName);
        $character->server = $server;
        $marketCharacter = null;
        if(MarketCharacter::inSell($character))
            $marketCharacter = MarketCharacter::where('character_id', $character->Id)->where('buy_date', null)->first();
        
        $user = $character->user();
        if(!$user)
             abort(404);
        $settings = $user->getCharactersSettings($server,$characterId);

        $idols =  $character->getJsonInventoryByItemType(178);
        $json =  $character->getJsonInventory();

        if(is_null($json) || is_null($idols))
        {
          $request->session()->flash('notify', ['type' => 'warning', 'message' => "Affichage impossible actuellement"]);
          return redirect()->back();
        }
        $itemsall = $character->getInventory($json);
        $itemscollection = collect($itemsall);
        $items = $this->collection_paginate($request, $itemscollection, 45);
        
        return view('gameaccount.character.inventory', compact('character', 'server', 'marketCharacter', 'idols', 'items', 'settings'));
    }

    function collection_paginate($request, $items, $per_page)
    {
        $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;
        if (!is_numeric($page)) {
            abort(404);
        }
        $offset = ($page * $per_page) - $per_page;

        return new LengthAwarePaginator(
            $items->forPage($page, $per_page)->values(),
            $items->count(),
            $per_page,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }


    public function recover(Request $request, $server, $accountId, $characterId)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        if (!World::isCharacterDeletedOwnedByMe($server, $accountId, $characterId)) {
            throw new GenericException('owner_error');
        }

        $account = Account::on($server . '_auth')->where('Id', $accountId)->first();
        $account->server = $server;

        if ($request->all()) {
            $deletedCharactersCount = count($account->DeletedCharacters(1));
            $character = Character::on($server . '_world')->where('Id', $characterId)->first();
            $price = $character->recoverPrice();
            $newname = $request->input('nickname');
            $oldname = $character->Name;

            $validator = Validator::make(['price_amount' => $price, 'nickname' => $newname], 
            ['price_amount' => 'required|integer|min:1|max:' . Auth::user()->points, 'nickname' => ['required', 'regex:/^[A-Z][a-z]{2,9}(?:-[A-Za-z][a-z]{2,9}|[a-z]{1,10})$/']]);

            if ($validator->fails()) {
                if ($validator->errors()->has('price_amount')) {
                    $validator->errors()->add('price', "Vous n'avez pas assez d'ogrines pour faire cet achat. Pour en acheter, rendez-vous sur notre Boutique.");
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if (!Character::on($server . '_world')->where([['Name', '=', $newname], ['Id', '!=', $characterId],])->first()) {
                if ((WorldCharacter::on($server . '_auth')->where('AccountId', $accountId)->count() - $deletedCharactersCount) < config('dofus.characters_limit')) {
                    Auth::user()->points -= $price;
                    Auth::user()->save();
                    $character->DeletedDate = null;
                    $character->Name = $newname;
                    $character->save();

                    $recoverCharacter = new RecoverCharacter;
                    $recoverCharacter->user_id = Auth::user()->id;
                    $recoverCharacter->points = $price;
                    $recoverCharacter->characterId = $characterId;
                    $recoverCharacter->oldName = $oldname;
                    $recoverCharacter->newName = $newname;
                    $recoverCharacter->save();

                    Cache::forget('characters_' . $server . '_' . $accountId);
                    Cache::forget('characters_deleted_' . $server . '_' . $accountId);

                    $request->session()->flash('notify', ['type' => 'success', 'message' => "Le personnage a correctement été récupéré. Bon jeu!"]);
                    return redirect()->route('gameaccount.view', [$server, $accountId]);
                } else {
                    $request->session()->flash('notify', ['type' => 'error', 'message' => "Vous avez trop de personnages sur ce compte. Le maximum est de ".config('dofus.characters_limit')."."]);
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('notify', ['type' => 'error', 'message' => "Le pseudo de ce personnage est déjà utilisé. Veuillez en choisir un autre ou le libérer en jeu"]);
                return redirect()->back()->withErrors(['nickname' => 'Le pseudo de ce personnage est déjà utilisé. Veuillez en choisir un autre ou le libérer en jeu'])->withInput();
            }
        }

        $character = Character::on($server . '_world')->where('Id', $characterId)->first();
        return view('gameaccount.recover', compact('account', 'character'));
    }

    private function urlValidationAndGetCharacter($server, $characterId, $characterName)
    {
        if (!World::isServerExist($server)) {
            throw new GenericException('invalid_server', $server);
        }

        $character = Cache::remember('character_view_'.$server.'_'.$characterId, 120, function () use($server, $characterId, $characterName) {
               return Character::on($server . '_world')->where('Id', $characterId)->where('Name', $characterName)->first();
        });
        if(!$character)
            abort(404);
        if((!MarketCharacter::inSell($character)) && (($character->level($server) < 20 && $character->PrestigeRank < 1) || $character->LastUsage < Carbon::today()->subMonths(6)->toDateString()))
            abort(404);

        $character->server = $server;
        
        return $character;
    }

    public function settings(Request $request, $server, $characterId, $characterName)
    {
        $character = $this->urlValidationAndGetCharacter($server,$characterId,$characterName);
        $settings = null;
        $json = json_decode(Auth::user()->settings);
        if(!$json)
            $json = new \stdClass;
        if(@!isset($json->characters))
            $json->characters = [];

        $collect = collect($json->characters);
        $templateToCheck = $collect->where('identifier', $characterId.'_'.$server)->first();

        if(!Auth::user()->isCharacterOwnedByMe($server,$characterId, true) && !MarketCharacter::inSell($character))
        { 
            abort(404);
        }

            if($request->isMethod('post'))
            {
                $rules = [
                'show_alignment' => 'sometimes|boolean',
                'show_ladder' => 'sometimes|boolean',
                'show_equipments' => 'sometimes|boolean',
                'show_spells' => 'sometimes|boolean',
                'show_caracteristics' => 'sometimes|boolean',
                'show_inventory' => 'sometimes|boolean',
                'show_idols' => 'sometimes|boolean',
                'history' => 'required|string|nullable|between:5,300',
                ];

                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                if($templateToCheck)
                {
                    $templateToCheck->show_alignment = $request->show_alignment ? $request->show_alignment : 0;
                    $templateToCheck->show_ladder = $request->show_ladder ? $request->show_ladder : 0;
                    $templateToCheck->show_equipments = $request->show_equipments ? $request->show_equipments : 0;
                    $templateToCheck->show_spells = $request->show_spells ? $request->show_spells : 0;
                    $templateToCheck->show_caracteristics = $request->show_caracteristics ? $request->show_caracteristics : 0;
                    $templateToCheck->show_inventory = $request->show_inventory ? $request->show_inventory : 0;
                    $templateToCheck->show_idols = $request->show_idols ? $request->show_idols : 0;
                    $templateToCheck->history = $request->history;
                    $templateToCheck->historyDate = Carbon::now()->format('d/m/Y');
                }
                else
                {
                    $new = new \stdClass;
                    $new->identifier = $characterId.'_'.$server;
                    $new->show_alignment = $request->show_alignment ? $request->show_alignment : 0;
                    $new->show_ladder = $request->show_ladder ? $request->show_ladder : 0;
                    $new->show_equipments = $request->show_equipments ? $request->show_equipments : 0;
                    $new->show_spells = $request->show_spells ? $request->show_spells : 0;
                    $new->show_caracteristics = $request->show_caracteristics ? $request->show_caracteristics : 0;
                    $new->show_inventory = $request->show_inventory ? $request->show_inventory : 0;
                    $new->show_idols = $request->show_idols ? $request->show_idols : 0;
                    $new->history = $request->history;
                    $new->historyDate = Carbon::now()->format('d/m/Y');
                                
                    array_push($json->characters, $new);
                }
            
                Auth::user()->settings = json_encode($json);
                Auth::user()->save();

                $request->session()->flash('notify', ['type' => 'success', 'message' => "Vos paramètres ont été mis à jour"]);
                return redirect()->route('characters.view', [$server,$characterId,$characterName]);
            }
            else
            {
                if($templateToCheck)
                {
                    $settings = $templateToCheck;
                }
                else
                {
                    $new = new \stdClass;
                    $new->identifier = $characterId.'_'.$server;
                    $new->show_alignment = 1;
                    $new->show_ladder = 1;
                    $new->show_equipments = 1;
                    $new->show_spells = 1;
                    $new->show_caracteristics = 1;
                    $new->show_inventory = 1;
                    $new->show_idols = 1;
                    $new->history = null;
                    $new->historyDate = null;

                    array_push($json->characters, $new);

                    Auth::user()->settings = json_encode($json);
                    Auth::user()->save();

                    $settings = $new;

                }
                return view('gameaccount.character.settings', compact('character', 'server', 'settings'));
            }

    }
}
