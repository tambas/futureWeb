<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Cache;
use App\Guild;
use App\Experience;
use App\Alignment;
use \DB;
use App\Helpers\Utils;
use App\Services\Stump;
use App\ItemPosition;

class Character extends Model
{
    protected $primaryKey = 'Id';

    protected $table = 'characters';

    protected $dates = ['CreationDate', 'LastUsage', 'DeletedDate'];

    public $timestamps = false;

    public $server;

    public function countStuff($server, $type, array $carrac)
    {
        $json = $this->getJsonInventoryEquiped();
        if(is_null($json))
            return '(not found)';
        if(!empty($json))
        {
            $value = Cache::remember('character_count_stuff_.'.$server.'_.'.$this->Id.'_.'.$type, 30, function () use ($json, $carrac) {
                $items = $json;
                if(is_null($items))
                    return ('not decode');
                $value = 0;
                foreach($items as $item)
                {
                    if(!in_array($item->Position,[63]))
                    {
                        foreach($item->Effects as $effect)
                        {
                            if(in_array($effect->Template->Id,$carrac))
                            {
                                if($effect->Template->Operator == '+')
                                    $value += $effect->Value;
                                else
                                    $value -= $effect->Value;
                            }
                        }
                    }
                }
                return $value;
            });
        }
        else
            $value = 0;

        return $value;
    }
    public function position($type = 'pvp', $spec = 'all', $server = 'sigma')
    {
        $resultDB = Cache::remember('position.'.$this->Id.'.'.$type.'.'.$spec.'.'.$server, 30, function () use ($server, $type, $spec) {
            $power = 0;
            if($type == 'xp')
                $power = $this->Experience + ($this->PrestigeRank * 3000000000000);
            $db = config('database.connections');
            $auth  = $db[$server.'_auth']['database'];
            $world = $db[$server.'_world']['database'];
        
            $result = DB::table($world.'.characters AS ch')
                ->leftJoin($auth.'.worlds_characters AS wc', 'ch.Id', '=', 'wc.CharacterId')
                ->leftJoin($auth.'.accounts AS acc', 'wc.AccountId', '=', 'acc.Id')
                ->where('acc.UserGroupId', 1);
                
                if($type == 'pvp')
                    $result = $result->where('ch.Honor', '>', $this->Honor);
                if($type == 'xp')
                    $result = $result->where(DB::raw('ch.Experience + (ch.PrestigeRank * 3000000000000)'), '>', $power);
                
                if($spec == 'breed')
                    $result = $result->where('Breed', $this->Breed);

                $result = $result->count('ch.Id');

            return Utils::format_price((int)($result + 1), ' ');
        });

        return $resultDB;
 
    }

    public function alignment()
    {
        $alignment = new Alignment($this->AlignmentSide, $this->Honor);
        return $alignment;
    }

    public function titleActive($server = 'sigma')
    {
        $titleactive =  Cache::remember('titleactive_'.$server.'_'.$this->TitleId, 1440, function () use($server) {
               return ModelCustom::hasOneOnOneServer('world', $server, Title::class, 'Id', $this->TitleId);
        });
       return $titleactive ? $titleactive : null;
    }

    public function ornamentActive($server = 'sigma')
    {
        $ornamentactive =  Cache::remember('ornamentactive'.$server.'_'.$this->Ornament, 1440, function () use($server) {
               return ModelCustom::hasOneOnOneServer('world', $server, Ornament::class, 'Id', $this->Ornament);
        });
       return $ornamentactive ? $ornamentactive : null;
    }

    public function items()
    {
        return $this->hasMany(CharacterItem::class, 'OwnerId', 'Id');
    }

    public function spells()
    {
        $spells = CharacterSpell::on($this->server.'_world')->where('OwnerId', $this->Id);
        return $spells;
    }

    public function guild($server = 'sigma')
    {
        $guildmember = Cache::remember('guildmember_'.$server.'_'.$this->Id, 120, function () use($server) {
               return ModelCustom::hasOneOnOneServer('world', $server, GuildMember::class, 'CharacterId', $this->Id);
        });

        if($guildmember)
        {
            $guild = Cache::remember('guild_'.$guildmember->GuildId.'.'.$server, 120, function () use($server, $guildmember) {
                return Guild::on($server.'_world')->findOrFail($guildmember->GuildId);
            });
            if($guild)
                return $guild;
            else
                return null;
        }
        return null;
    }
    public function maxLifePoints()
    {
        $result = $this->BaseHealth + $this->Vitality + $this->PermanentAddedVitality + $this->countStuff($this->server,'vitality',['125','153']);
        return $result;
    }
    public function actualLifePoints()
    {
        $result = $this->maxLifePoints() - $this->DamageTaken;
        return $result;
    }
    public function countBaseInitiative()
    {
        $agility = $this->Agility + $this->PermanentAddedAgility + $this->countStuff($this->server,'agility',['119','154']);
        $chance = $this->Chance + $this->PermanentAddedChance + $this->countStuff($this->server,'chance',['123','152']);
        $intelligence = $this->Intelligence + $this->PermanentAddedIntelligence + $this->countStuff($this->server,'intelligence',['126','155']);
        $strength = $this->Strength + $this->PermanentAddedStrength + $this->countStuff($this->server,'strength',['118','157']);

        $result = $agility + $chance + $intelligence + $strength;
        return $result;
    }
    public function tempExp()
    {
        $tempExp = $this->Experience;

        if (config('dofus.details')[$this->server]->prestige) {
            $maxExp = Cache::remember('exp_' . $this->server . '_max', 1440, function (){
                return Experience::on($this->server . '_world')->orderBy('CharacterExp', 'desc')->first();
            });

            if ($maxExp) {
                $tempExp = $this->Experience - ($this->PrestigeRank * $maxExp->CharacterExp);
            }
        }
        return $tempExp;
    }
    public function expNextLevel()
    {
        $level = $this->level($this->server);
        if($level != 200)
            $seekLevel = $level + 1;
        else
            $seekLevel = $level;
        $experience = Experience::on($this->server . '_world')->select('CharacterExp')->where('Level', $seekLevel)->first();
        return $experience ? $experience->CharacterExp : "1";
    }
    public function level($server = 'sigma')
    {
        $tempExp = $this->Experience;

        if (config('dofus.details')[$server]->prestige) {
            $maxExp = Cache::remember('exp_' . $server . '_max', 1440, function () use ($server) {
                return Experience::on($server . '_world')->orderBy('CharacterExp', 'desc')->first();
            });

            if ($maxExp) {
                $tempExp = $this->Experience - ($this->PrestigeRank * $maxExp->CharacterExp);
            }
        }

        $exp = Cache::remember('exp_' . $server . '_' . $tempExp, 1440, function () use ($server, $tempExp) {
            return Experience::on($server . '_world')->where('CharacterExp', '<=', $tempExp)->orderBy('Level', 'desc')->first();
        });

        if ($exp) {
            return $exp->Level;
        } else {
            return 1;
        }
    }

    public function classe()
    {
         $classes = [
            1  => 'Féca',
            2  => 'Osamodas',
            3  => 'Enutrof',
            4  => 'Sram',
            5  => 'Xélor',
            6  => 'Ecaflip',
            7  => 'Eniripsa',
            8  => 'Iop',
            9  => 'Crâ',
            10 => 'Sadida',
            11 => 'Sacrieur',
            12 => 'Pandawa',
            13 => 'Roublard',
            14 => 'Zobal',
            15 => 'Steamer',
            16 => 'Eliotrope',
            17 => 'Huppermage',
            18 => 'Ouginak'
         ];
        
         return array_key_exists($this->Breed, $classes) ? $classes[$this->Breed] : "Classe inconnue";
    }

    public function alignement()
    {
        $alignement = [
            0 => [ 'Neutre',     '' ],
            1 => [ 'Bontarien',  '#59d7ff' ],
            2 => [ 'Brakmarien', '#ff6161' ],
            3 => [ 'Mercenaire', '#7d6b54' ],
        ];

        $align = $alignement[$this->AlignmentSide];

        return '<font color="' . $align[1] . '">' . $align[0] . '</font>';
    }

    public function sex()
    {
        if($this->Sex == 0)
            return "Mâle";
        else
            return "Femelle";
    }

    public function isDeleted()
    {
        return $this->DeletedDate ? true : false;
    }

    public function scopeDeleted($query)
    {
        $query->where('DeletedDate', '!=', null);
    }

    public function account($server)
    {
        $worldCharacter = ModelCustom::hasOneOnOneServer('auth', $server, WorldCharacter::class, 'CharacterId', $this->Id);
        return $worldCharacter ? $worldCharacter->account() : null;
    }
    public function user()
    {
        $account = $this->account($this->server);
        if($account)
        {
             $user = User::where('email', $account->Email)->first();
                return $user ? $user : null;
        }
        return null;
    }

    public function recoverPrice()
    {
        $price = $this->level() * config('dofus.recover_level_multiplicator_price');
        return $price < config('dofus.recover_minimal_price') ? config('dofus.recover_minimal_price') : $price;
    }

    public function getJsonInventory()
    {
        $json = Cache::remember('character_inventory_json_'.$this->server.'_'.$this->Id, 30, function (){
            $json = Stump::get($this->server, "/Character/$this->Id/Inventory");
            return json_decode($json);
        });

        return $json;
    }
    public function getJsonInventoryEquiped()
    {
        $json = Cache::remember('character_inventory_equiped_json_'.$this->server.'_'.$this->Id, 30, function (){
            $json = Stump::get($this->server, "/Character/$this->Id/Inventory/Equiped");
            return json_decode($json);
        });
        return $json;
    }

    public function getJsonInventoryByItemType($typeId)
    {
        $json = Cache::remember('character_inventory_json_'.$this->server.'_'.$this->Id.'_'.$typeId, 30, function () use($typeId){
            $json = Stump::get($this->server, "/Character/$this->Id/Inventory/Type/$typeId");
            return json_decode($json);
        });

        return $json;
    }

    public function getEquipment($json)
    {
        $itemsall = array('left' => [], 'right' => [], 'bottom' => [], 'costume' => []);

        if(!$json)
            return $itemsall;
        $itemsall = Cache::remember('character_equipment_'.$this->server.'_'.$this->Id, 30, function () use($json, $itemsall) {
               $items = $json;
                foreach($items as $item)
                {
                    switch($item->Position)
                    {
                        case ItemPosition::ACCESSORY_POSITION_SHIELD:
                        case ItemPosition::ACCESSORY_POSITION_AMULET:      
                        case ItemPosition::INVENTORY_POSITION_RING_LEFT:
                        case ItemPosition::ACCESSORY_POSITION_CAPE:
                        case ItemPosition::ACCESSORY_POSITION_BOOTS:
                            array_push($itemsall['left'], $item);
                        break;
                        case ItemPosition::ACCESSORY_POSITION_WEAPON:
                        case ItemPosition::ACCESSORY_POSITION_HAT:      
                        case ItemPosition::INVENTORY_POSITION_RING_RIGHT:
                        case ItemPosition::ACCESSORY_POSITION_BELT:
                        case ItemPosition::ACCESSORY_POSITION_PETS:
                            array_push($itemsall['right'], $item);
                        break;
                        case ItemPosition::INVENTORY_POSITION_DOFUS_1:
                        case ItemPosition::INVENTORY_POSITION_DOFUS_2:
                        case ItemPosition::INVENTORY_POSITION_DOFUS_3:
                        case ItemPosition::INVENTORY_POSITION_DOFUS_4:
                        case ItemPosition::INVENTORY_POSITION_DOFUS_5:
                        case ItemPosition::INVENTORY_POSITION_DOFUS_6:
                            array_push($itemsall['bottom'], $item);
                        break;
                        case ItemPosition::INVENTORY_POSITION_COSTUME:
                            array_push($itemsall['costume'], $item);
                        break;
                        default:
                        break;
                    }
                }
                return $itemsall;
        });
        return $itemsall;
    }

    public function getInventory($json)
    {
        if(!$json)
            return [];
        $items = Cache::remember('character_inventory_'.$this->server.'_'.$this->Id, 30, function () use($json) {
               $items = [];
               $allitems = $json;
                foreach($allitems as $item)
                {
                    switch($item->Position)
                    {
                        case ItemPosition::INVENTORY_POSITION_NOT_EQUIPED:
                            array_push($items, $item);
                        break;
                        default:
                        break;
                    }
                }
                return $items;
        });
        return $items;
    }

    public static function KolizeumRatio($total, $wins)
    {
        $ratio = ['value' => '-', 'type' => 'none'];
        if($total != 0)
        {
            $ratio['value'] = (($wins / $total) * 100);
            if($ratio['value'] < 50)
                $ratio['type'] = 'neg';
            if($ratio['value'] == 50)
                $ratio['type'] = 'equal';
            if($ratio['value'] > 50)
                $ratio['type'] = 'pos';
        }
            return $ratio;
    }
}
