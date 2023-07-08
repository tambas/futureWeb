<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redis;
use Illuminate\Support\Facades\Cache;
use App\Character;
use App\Services\Linker;
use App\Characteristic;
use Illuminate\Pagination\Paginator;
use App\World;
use App\Services\DofusForge;
use App\ItemTemplate;
use App\Effects;
use App\SpellTemplate;
use App\Emoticons;

class LinkerController extends Controller
{
    public function get(Request $request)
    {
        $server = $request->input('server');
        $characterId = $request->input('character');
        $position = $request->input('position');
        if(!$server || !$characterId || !$position)
            return response()->json([], 402);
        if(!in_array($position, ['right', 'left', 'bottom', 'costume', 'inventory', 'idols']))
             return response()->json([], 402);
        if(!is_numeric($characterId))
            return response()->json([], 402);
        if(!World::isServerExist($server))
             return response()->json([], 402);
        $character = Cache::get('character_view_'.$server.'_'.$characterId);
        if(!$character)
            return response()->json([], 402);

        $character->server = $server;
        $id = $request->input('id');
        if(!is_numeric($id))
            return response()->json([], 402);

        if(in_array($position, ['right', 'left', 'bottom', 'costume']))
        {
            $itemsall = Cache::get('character_equipment_'.$server.'_'.$characterId);
            if(!$itemsall)
            {
                $json = $character->getJsonInventoryEquiped();
                $itemsall = $character->getEquipment($json);
                if(!$itemsall)
                   return view('templates.linker_error');
            }
            $collection = collect($itemsall[$position]);
        }
        elseif($position == 'inventory')
        {
            $itemsall = Cache::get('character_inventory_'.$server.'_'.$characterId);
            if(!$itemsall)
            {
                $json = $character->getJsonInventory();
                $itemsall = $character->getInventory($json);
                if(!$itemsall)
                    return view('templates.linker_error');
            }
            $collection = collect($itemsall);
        }
        elseif($position == 'idols')
        {
            $itemsall = Cache::get('character_inventory_json_'.$server.'_'.$characterId.'_178');
            if(!$itemsall)
            {
                $itemsall = $character->getJsonInventoryByItemType(178);
                if(!$itemsall)
                    return view('templates.linker_error');
            }
            $collection = collect($itemsall);
        }
        $item = $collection->where('Id', $id)->first();
        if(!$item)
             return view('templates.linker_error');
        $effects = $item->Effects;
        $effectArray = [];
        foreach($effects as $k => $effect)
        {
            if($effect->Template->DescriptionId == 0 && $effect->EffectId == Effects::Effect_LivingObjectId)
            {
                $itemid = $effect->Value;
                $itemtemp = ItemTemplate::on($server . '_world')->where('id', $itemid)->first();
                if($itemtemp)
                {
                    $itemname = $itemtemp->name();
                    $text = 'Fusionné avec: '.$itemname.'';
                    $asset = null;
                    $name = null;
                    $finish = collect(['text' => $text, 'asset' => $asset, 'name' => $name]);
                    array_push($effectArray, $finish);
                }
            }
            if($effect->Template->DescriptionId != 0)
            {
                if($effect->EffectId != Effects::Effect_LastMealDate && $effect->EffectId != Effects::Effect_Corpulence)
                {
                    if(@isset($effect->Template->Description))
                        $text = $effect->Template->Description;
                    else
                        $text = DofusForge::text($effect->Template->DescriptionId, $server);
                
                    if(@!isset($effect->Value)) 
                        $effect->Value = "-Value not found-";
                    $value = $effect->Value;
                    if($text && $effect->Template->UseDice)
                    {
                    if($value != 0)
                            $text = preg_replace('/#1.*#2/', $value, $text);
                        elseif(@isset($effect->DiceNum) && @isset($effect->DiceFace))
                        {
                            $tochange = ''.$effect->DiceNum.' à '.$effect->DiceFace.'';
                            $text = preg_replace('/#1.*#2/', $tochange, $text);
                        }
                    }
                    elseif($text && !$effect->Template->UseDice)
                    {
                        if($value != 0)
                        {
                            if($effect->EffectId == Effects::Effect_Emoticons)
                            {
                                $emoticons = Emoticons::on($server . '_data')->where('Id', $value)->first();
                                if($emoticons)
                                {
                                    $emoticons->server = $server;
                                    $text = preg_replace('/#3/', $emoticons->name(), $text);
                                }
                            }
                            else
                                $text = preg_replace(['/#1/','/#2/','/#3/','/#4/'], $value, $text);
                        }
                        elseif($value == 0)
                        {
                            if($effect->EffectId == Effects::Effect_SpellItem && @isset($effect->DiceNum) && @isset($effect->DiceFace))
                            {
                                $spellId = $effect->DiceNum;
                                $spell = SpellTemplate::on($server . '_world')->where('id', $spellId)->first();
                                if($spell)
                                {
                                    $spell->server = $server;
                                    $text = $spell->description();
                                }
                            }
                        }
                    }     
                    $characteristic = Characteristic::where('id', $effect->Template->Characteristic)->first();

                    $name = null;
                    $asset = null;
                    if($characteristic)
                    {
                        if(!empty($characteristic->asset))
                        $asset = strtolower(str_replace('_', '-', $characteristic->asset));
                        $name = $characteristic->name;
                    }
                    $finish = collect(['text' => $text, 'asset' => $asset, 'name' => $name]);
                    if($text != "")
                        array_push($effectArray, $finish);
                }
            }
            
        }       
        $perPage = 6;
        $newEffectArray = [];
        $totalPages = (int)ceil(count($effectArray)/$perPage);
        for($x = 1; $x <= $totalPages; $x++)
        {
            $offSet = ($x * $perPage) - $perPage; 
            $newarray = array_slice($effectArray, $offSet, $perPage);
            array_push($newEffectArray, $newarray);
        }
        return view('templates.linker', compact('item', 'newEffectArray'));
    }
}
