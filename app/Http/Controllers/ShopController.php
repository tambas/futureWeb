<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Character;
use App\MarketCharacter;
use Cache;
use \DB;
use App\Experience;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\WorldCharacter;
use App\Services\Stump;
use App\Account;
use App\User;

class ShopController extends Controller
{
    const CHARACTERS_PER_PAGE = 20;

    public function index()
    {
        return view('shop.index');
    }

    public function details(Request $request, $article)
    {
        return view('shop.details');
    }

    public function market(Request $request)
    {
        $page = $request->has('page') && is_numeric($request->input('page')) ? $request->input('page') : 1;
        if (!is_numeric($page)) {
            abort(404);
        }

        // FILTERS INIT
        $filters = [
            'character_homeserv' => ['values' => []], 
            'character_breed_id' => ['values' => []], 
            'character_sex'      => ['values' => []],
            'character_level'    => ['values' => []],
            'character_prestige' => ['values' => []],
            'character_price'    => ['values' => []]
        ];
        if(count($request->all()) > 0) // REQUEST WITH FILTERS
        {
            // VALIDATION
            $rules = [
            'character_homeserv'      => 'sometimes|array|distinct|serverarray',
            'character_breed_id'      => 'sometimes|array|distinct|breedarray',
            'character_sex'           => 'sometimes|array|distinct|sexarray',

            'TEXT'                    => 'present|nullable|alpha_dash',
            'character_level_min'     => 'required|numeric|between:1,200',
            'character_level_max'     => 'required|numeric|between:1,200',
            'character_prestige_min'  => 'required|numeric|between:0,15',
            'character_prestige_max'  => 'required|numeric|between:0,15',
            'character_price_min'     => 'present|nullable|numeric',
            'character_price_max'     => 'present|nullable|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if (!$validator->fails()) 
            {
                if($request->character_homeserv)
                {
                    $servers = $request->character_homeserv;
                    $filters['character_homeserv'] = ['values' => $servers, 'name' => 'Serveurs', 'text' => $servers, 'separator' => ','];
                }
                else
                    $servers = config('dofus.servers');

                $filters['TEXT'] = ['values' => $request->TEXT, 'name' => 'Nom'];

                $levels = [$request->character_level_min,$request->character_level_max];
                $filters['character_level'] = ['values' => $levels, 'name' => 'Niveau', 'text' => $levels, 'separator' => ' à'];

                foreach($servers as $server)
                {
                    $min_exp = Experience::on($server .'_world')->select('CharacterExp')->where('Level', $request->character_level_min)->first();
                    $max_exp = Experience::on($server .'_world')->select('CharacterExp')->where('Level', $request->character_level_max)->first();

                    $exp_min[$server] = $min_exp->CharacterExp;
                    $exp_max[$server] = $max_exp->CharacterExp;
                    $dbMaxExp[$server] = Experience::maxExp($server);
                }   
                if($request->character_breed_id)
                {
                    $textbreeds = $request->character_breed_id;
                    $textbreeds = str_replace(["10","11","12","13","14","15","16","17","18","1","2","3","4","5","6","7","8","9"], ['Sadida', 'Sacrieur', 'Pandawa', 'Roublard', 'Zobal', 'Steamer', 'Eliotrope', 'Huppermage', 'Ouginak', 'Féca', 'Osamodas', 'Enutrof', 'Sram', 'Xélor', 'Ecaflip', 'Eniripsa', 'Iop', 'Crâ'], $textbreeds);
                    $filters['character_breed_id'] = ['values' => $request->character_breed_id, 'name' => 'Classe', 'text' => $textbreeds, 'separator' => ','];
                }
                
                if($request->character_sex)
                {
                    $textsex = $request->character_sex;
                    $textsex = str_replace([0,1], ['Mâle','Femelle'], $textsex);
                    $filters['character_sex'] = ['values' => $request->character_sex, 'name' => 'Sexe', 'text' => $textsex, 'separator' => ','];
                }

                $prestiges = [$request->character_prestige_min,$request->character_prestige_max];
                $filters['character_prestige'] = ['values' => $prestiges, 'name' => 'Prestige', 'text' => $prestiges, 'separator' => ' à'];

                if($request->character_price_min || $request->character_price_max)
                    $prices = [$request->character_price_min,$request->character_price_max];
                else
                    $prices = [];
                $filters['character_price'] = ['values' => $prices, 'name' => 'Prix', 'text' => $prices, 'separator' => ' à'];
                
                $market_characters = Cache::tags(['marketfiltered'])->get('market_characters_filtered_'.$request->fullUrl().'_'.$page);

                if(!$market_characters)
                {
                    $db = config('database.connections');
                    $ids = [];
                    // Finds id's of filtered market characters
                    foreach($servers as $server)
                    {
                        $world = $db[$server.'_world']['database'];
                        $elements = DB::table('market_characters AS mk')
                                            ->where('mk.server', $server)
                                            ->select('mk.id', 'mk.character_id', 'mk.server')
                                            ->leftJoin($world.'.characters AS ch', 'ch.Id', '=', 'mk.character_id');

                        if($request->TEXT)
                            $elements = $elements->where('ch.Name', 'like', '%'.$request->TEXT.'%');
                        if($request->character_breed_id)
                            $elements = $elements->whereIn('ch.Breed', $request->character_breed_id);
                        if($request->character_sex)
                            $elements = $elements->whereIn('ch.Sex', $request->character_sex);

                        if($request->character_level_max != 200)
                            $elements = $elements->whereBetween(DB::raw('ch.Experience - (ch.PrestigeRank * '.$dbMaxExp[$server].')'), [$exp_min[$server], $exp_max[$server]]);
                        else
                            $elements = $elements->where(DB::raw('ch.Experience - (ch.PrestigeRank * '.$dbMaxExp[$server].')'), '>=', $exp_min[$server]);
                        
                        $elements = $elements->whereBetween('ch.PrestigeRank', [$request->character_prestige_min, $request->character_prestige_max]);

                        if($request->character_price_min)
                            $elements = $elements->where('mk.ogrines', '>=', $request->character_price_min);
                        if($request->character_price_max)
                            $elements = $elements->where('mk.ogrines', '<=', $request->character_price_max);

                        $elements = $elements->get();

                        foreach($elements as $element)
                        {
                            array_push($ids,$element->character_id);
                        }
                    }
                    $market_characters = MarketCharacter::latest('created_at')->whereIn('character_id', $ids)->insell()->paginate(self::CHARACTERS_PER_PAGE);
                    Cache::tags(['marketfiltered'])->put('market_characters_filtered_'.$request->fullUrl().'_'.$page, $market_characters, 30);
                }
            }
            else
            {
                $market_characters = Cache::tags(['market'])->get('market_characters'.$page);
                if(!$market_characters)
                {
                    $market_characters = MarketCharacter::latest('created_at')->insell()->paginate(self::CHARACTERS_PER_PAGE);
                    Cache::tags(['market'])->put('market_characters_filtered_'.$request->fullUrl().'_'.$page, $market_characters, 30);
                }
            }
        }
        else // REQUEST WITHOUT FILTERS
        {
            $market_characters = Cache::tags(['market'])->get('market_characters'.$page);
            if(!$market_characters)
            {
                $market_characters = MarketCharacter::latest('created_at')->insell()->paginate(self::CHARACTERS_PER_PAGE);
                Cache::tags(['market'])->put('market_characters_filtered_'.$request->fullUrl().'_'.$page, $market_characters, 30);
            }
        }
        return view('shop.market.index', compact('market_characters', 'filters'));
    }

    public function marketSell(Request $request)
    {
        if($request->isMethod('post'))
        {
            // VALIDATION
            $rules = [
                'server'      => 'required|alpha_dash|serverarray',
                'character'   => 'required|numeric|integer',
                'ogrines'     => 'required|numeric|integer|min:'.config('dofus.characters_market.minimal_price').'',
            ];
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                 return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }

            // SERVER IS VALID
            $server = $request->input('server');

            // CHECK IF REQUESTER IS THE OWNER OF THE CHARACTER
            if(!Auth::user()->isCharacterOwnedByMe($server,$request->input('character'), true)) // Character owned by user ? true = minimal
            { 
                $validator->errors()->add('character', "Problème avec le personnage sélectionné");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }

            // GET CHARACTER WE WANT TO SELL
            $character = Character::on($server . '_world')->where('Id', $request->character)->where('DeletedDate',null)->first();
            if(!$character)
            {
                $validator->errors()->add('character', "Ce personnage est invalide");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }
            // CHECK IF ACCOUNT OF CHARACTER IS NOT JAIL OR BANNED
            $account = $character->account($server);
            if(!$account)
            {
                $validator->errors()->add('character', "Problème de compte de jeu");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }
            if($account->IsJailed == 1 || $account->isBanned() || $account->isStaff())
            {
                $validator->errors()->add('character', "Problème de compte de jeu");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }

            // CHECK IF CHARACTER IS NOT ALREADY IN SELL
            if(MarketCharacter::inSell($character)) // Check if buy date null
            { 
                $validator->errors()->add('character', "Ce personnage est déjà en vente");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }

            // CHECK IF PLAYER CONNECTED
            $playerConnected = Stump::get($server, '/Account/'.$account->Id.'');
            if($playerConnected)
            {
                $validator->errors()->add('character', "Vous devez être déconnecté du compte en jeu");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }

            // CALCUL TAXE
            $procent = config('dofus.characters_market.procent_taxe');
            $taxe = (int)ceil((($request->ogrines / 100) * $procent));

            // CHECK IF USER HAS ENOUGH POINTS
            if($taxe > Auth::user()->points)
            {
                $validator->errors()->add('taxe', "Vous n'avez pas assez d'ogrines pour payer la taxe de mise en vente.<br>Baissez le prix de vente ou achetez des ogrines sur notre Boutique");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }

            // CHECK NUMBER OF MARKET CHARACTER PAR USER IN SAME TIME
            $numberOfMarketCharacters = MarketCharacter::where('user_id', Auth::user()->id)->where('buy_date',null)->count();
            if($numberOfMarketCharacters >= config('dofus.characters_market.maximum_per_user'))
            {
                $validator->errors()->add('character', "Vous avez atteint le nombre maximum de personnages en vente simultanément ( ".config('dofus.characters_market.maximum_per_user')." )");
                return redirect()->to(app('url')->previous(). '#sellplace')->withErrors($validator)->withInput();
            }

            // TAKE TAXE FROM USER
            Auth::user()->points -= $taxe;
            Auth::user()->save();

            // DELETE CHARACTER FROM DB.WORLD
            $character->DeletedDate = Carbon::now();
            $character->save();

            // ADD NEW MARKET CHARACTER
            $marketCharacter = new MarketCharacter;
            $marketCharacter->user_id = Auth::user()->id;
            $marketCharacter->ogrines = $request->ogrines;
            $marketCharacter->character_id = $request->character;
            $marketCharacter->character_name = $character->Name;
            $marketCharacter->server = $server;
            $marketCharacter->save();

            // GET ACCOUNT ID TO CLEAR CACHE
            $accountId = $character->account($server)->Id;
            // CLEAR CACHE
            Cache::forget('characters_user_'.Auth::user()->id.'_1');
            Cache::forget('characters_user_'.Auth::user()->id.'_');
            Cache::forget('characters_'.$server.'_'.$accountId.'_1');
            Cache::forget('characters_'.$server.'_'.$accountId.'_');

            Cache::tags(['marketfiltered'])->flush();
            Cache::tags(['market'])->flush();

            $request->session()->flash('notify', ['type' => 'success', 'message' => "Votre personnage a correctement été mis en vente"]);
            return redirect()->route('history.market');
        }
        else
            return view('shop.market.sell');
    }

    public function marketRemove(Request $request)
    {
         // VALIDATION
        $validator = Validator::make($request->all(), 
        ['marketid' => 'required|integer|exists:market_characters,id']);
        if($validator->fails())
        {
            return response()->json([], 400);
        }

        // GET MARKET CHARACTER IN SELL
        $marketCharacter = MarketCharacter::where('buy_date', null)->findOrFail($request->marketid);

        // CHECK IF REQUESTER IS OWNER OF THIS MARKET CHARACTER
        if(Auth::user()->id != $marketCharacter->user_id)
            return response()->json([], 403);

        // CHECK NBR OF CHARACTERS ON ACCOUNT
        $worldCharacter = WorldCharacter::on($marketCharacter->server . '_auth')->where('CharacterId', $marketCharacter->character_id)->first();
        $worldCharacter->server = $marketCharacter->server;
        $numberOfCharacters = count($worldCharacter->account()->characters(true, false)); // NO CACHE & ALL CHAR
        if($numberOfCharacters >= config('dofus.characters_limit'))
            return response()->json(['title' => ['0' => 'Impossible de retirer le personnage de la vente. Vous avez trop de personnages sur le compte de jeu: '.$worldCharacter->account()->Login.'. Le maximum est de '.config('dofus.characters_limit').'']], 400);
        
        // CHECK IF PLAYER CONNECTED
        $character = $worldCharacter->character();
        $playerConnected = Stump::get($marketCharacter->server, '/Account/'.$worldCharacter->account()->Id.'');
        if($playerConnected)
        {
            return response()->json(['title' => ['0' => 'Vous devez être déconnecté du compte en jeu']], 400);
        }

        // DELETE MARKET CHARACTER
        $marketCharacter->delete();
        // RESTORE CHARACTER
        $character->DeletedDate = null;
        $character->save();

        // CLEAR CACHE
        Cache::forget('characters_user_'.Auth::user()->id.'_1');
        Cache::forget('characters_user_'.Auth::user()->id.'_');
        Cache::forget('characters_'.$marketCharacter->server.'_'.$worldCharacter->account()->Id.'_1');
        Cache::forget('characters_'.$marketCharacter->server.'_'.$worldCharacter->account()->Id.'_');

        Cache::tags(['marketfiltered'])->flush();
        Cache::tags(['market'])->flush();

        return response()->json([], 200);
    }

    public function marketBuy(Request $request, $id)
    {
        $marketCharacter = MarketCharacter::where('buy_date', null)->findOrFail($id);
        // GET CHARACTER
        $character = Character::on($marketCharacter->server . '_world')->where('Id', $marketCharacter->character_id)->where('DeletedDate', '!=', null)->first(); // "Deleted" character
        if(!$character)
        {
            $request->session()->flash('notify', ['type' => 'error', 'message' => "Ce personnage ne semble plus être en vente"]);
            return redirect()->route('shop.market');
        }
        $server = $marketCharacter->server;

        if($request->isMethod('post'))
        {
            // VALIDATION
            $validator = Validator::make($request->all(), 
            ['account' => 'required|integer', 'charactername' => ['required', 'regex:/^[A-Z][a-z]{2,9}(?:-[A-Za-z][a-z]{2,9}|[a-z]{1,10})$/']]);

            if($validator->fails())
            {
                 return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }

            // CHECK IF ACCOUNT REQUESTED IS MINE
            if(!Auth::user()->isAccountOwnedByMe($server,$request->account))
            {
                $validator->errors()->add('account', "Problème avec le compte de jeu");
                return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }

            // CHECK IF ACCOUNT SELLED (NOT BUYER!!) IS NOT JAIL OR BANNED
            $accountSelled = $character->account($server);
            if(!$accountSelled)
            {
                $validator->errors()->add('account', "Problème de compte de jeu");
                return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }
            if($accountSelled->IsJailed == 1 || $accountSelled->isBanned() || $accountSelled->isStaff())
            {
                $validator->errors()->add('account', "Achat impossible. Le personnage que vous souhaitez acheter est actuellement sanctionné.");
                return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }

            // GET ACCOUNT OF NEW PROPR
            $account = Account::on($server . '_auth')->where('Id', $request->account)->first();
            $account->server = $server;

            // CHECK NBR OF CHARACTERS ON ACCOUNT
            $numberOfCharacters = count($account->characters(true, false)); // NO CACHE & ALL CHAR
            if($numberOfCharacters >= config('dofus.characters_limit'))
            {
                $validator->errors()->add('account', 'Vous avez trop de personnages sur le compte de jeu. Le maximum est de '.config('dofus.characters_limit').'');
                return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }

            // CHECK IF PLAYER CONNECTED
            $playerConnected = Stump::get($server, '/Account/'.$account->Id.'');
            if($playerConnected)
            {
               $validator->errors()->add('account', 'Vous devez être déconnecté du compte de jeu');
               return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }

            // CHECK IF USER HAS ENOUGH POINTS TO BUY
            if($marketCharacter->ogrines > Auth::user()->points)
            {
                $validator->errors()->add('recap', "Vous n'avez pas assez d'ogrines pour faire cet achat.<br>Achetez des ogrines sur notre Boutique");
                return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }

            // CHECK IF NEW NAME IS NOT ALREADY USED
            if(Character::on($server . '_world')->where('Name', $request->charactername)->where('DeletedDate', null)->first())
            {
                return redirect()->to(app('url')->previous(). '#buyplace')->withErrors($validator)->withInput();
            }
            // TAKE PRICE OF CHARACTER FROM BUYER
            Auth::user()->points -= $marketCharacter->ogrines;
            Auth::user()->save();

            // GET WORLD CHARACTER
            $character = Character::on($server . '_world')->where('Id', $marketCharacter->character_id)->where('DeletedDate', '!=', null)->first(); // "Deleted" character
            $worldCharacter = WorldCharacter::on($server . '_auth')->where('CharacterId', $marketCharacter->character_id)->first();
            // CHANGE ACCOUNT PROPR
            $worldCharacter->AccountId = $account->Id;
            $worldCharacter->save();
            // ADD BUYER AND BUY DATE
            $marketCharacter->buy_date = Carbon::now();
            $marketCharacter->buyer_id = Auth::user()->id;
            $marketCharacter->save();
            // RESTORE CHARACTER AND CHANGE NAME
            $character->DeletedDate = null;
            $character->Name = $request->charactername;
            $character->save();

            // ADD OGRINES TO ORIGINAL SELLER
            $seller = User::findOrFail($marketCharacter->user_id);
            $seller->points += $marketCharacter->ogrines;
            $seller->save();

            // CLEAR CACHE
            Cache::tags(['marketfiltered'])->flush();
            Cache::tags(['market'])->flush();
            Cache::forget('characters_user_'.Auth::user()->id.'_1');
            Cache::forget('characters_user_'.Auth::user()->id.'_');
            Cache::forget('characters_'.$server.'_'.$account->Id.'_1');
            Cache::forget('characters_'.$server.'_'.$account->Id.'_');
            Cache::forget('character_view_'.$server.'_'.$character->Id.'');

            $request->session()->flash('notify', ['type' => 'success', 'message' => "Vous avez bien reçu votre nouveau personnage! Bon jeu"]);
            return redirect()->route('gameaccount.view', [$server,$account->Id]);
        }
        else
        {
            if(Auth::user()->id == $marketCharacter->user_id)
                abort('404');
           if(Auth::user()->points < $marketCharacter->ogrines)
            {
                $request->session()->flash('notify', ['type' => 'info', 'message' => "Vous n'avez pas assez d'ogrines pour faire cet achat"]);
                return redirect()->route('shop.market');
            }
            return view('shop.market.buy', compact('marketCharacter', 'character', 'server'));
        }
    }

}
