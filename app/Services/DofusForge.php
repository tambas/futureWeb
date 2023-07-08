<?php

namespace App\Services;

use App\Character;
use App\Lang;
use Redis;

class DofusForge
{
    public static function asset($url)
    {
        return url('/') . '/forge/image/' . $url;
    }

    public static function image($request)
    {
        $redis = Redis::connection();

        $format = pathinfo($request, PATHINFO_EXTENSION);
        switch ($format) {
            case 'png':
            default:
                header('Content-Type: image/png');
                break;
        }

        $url = "http://staticns.ankama.com/";
        $url .= $request;
        $hash = md5($request);

        $data = $redis->get("dofus:forge:$hash:$request");

        if ($data) {
            return $data;
        } else 
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_COOKIESESSION, true);
            $result = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($code != 200) {
                header("HTTP/1.1 404 Not Found");
                header('Content-Type: plain/text');
                return $result;
            } elseif($code == 200) {
                $redis->set("dofus:forge:$hash:$request", $result);
                return $result;
            }

            curl_close($curl);
        }
    }

    public static function player(Character $character, $server, $mode, $orientation, $sizeX, $sizeY, $margin = 0)
    {
            if(config('dofus.details')[$server]->version == "2.10")
            {
                $look = bin2hex(self::purifyLook($character->EntityLookString));
            }
            else
            {
                $jsonLook = Stump::get($server, "/Character/$character->Id/Look");
                $look = json_decode($jsonLook);

                if(!$look)
                    $look = $character->DefaultLookString;

                $look = bin2hex(self::purifyLook($look));
            }

            return self::asset("dofus/renderer/look/$look/$mode/$orientation/{$sizeX}_{$sizeY}-{$margin}.png");
    }
    public static function playerId($characterId, $server, $mode, $orientation, $sizeX, $sizeY, $margin = 0)
    {
        $character = Character::on($server.'_world')->where('Id', $characterId)->first();
        if($character)
        {
            if(config('dofus.details')[$server]->version == "2.10")
            {
                $look = bin2hex(self::purifyLook($character->EntityLookString));
            }
            else
            {
                $jsonLook = Stump::get($server, "/Character/$character->Id/Look");
                $look = json_decode($jsonLook);

                if(!$look)
                    $look = $character->DefaultLookString;

                $look = bin2hex(self::purifyLook($look));
            }

            return self::asset("dofus/renderer/look/$look/$mode/$orientation/{$sizeX}_{$sizeY}-{$margin}.png");
        }
        return null;
    }

    public static function item($iconId, $size)
    {
        return self::asset('dofus/www/game/items/'.$size.'/' . $iconId . '.png');
    }

    public static function spell($iconId, $size)
    {
        return self::asset('dofus/www/game/spells/'.$size.'/sort_' . $iconId . '.png');
    }

    public static function text($id, $server = null)
    {
        if(!$server)
            $server = config('dofus.servers')[0];

        $redis = Redis::connection();

        $text = $redis->get("dofus:forge:text:$server:$id");

        if ($text)
        {
            return $text;
        }
        else
        {
            $lang = Lang::on($server . '_world')->select('French')->where('Id', $id)->first();
            if(!$lang)
                $text = "Text not found.";

            if ($lang && $lang->French)
            {
                $redis->set("dofus:forge:text:$server:$id", $lang->French);
                $text = $lang->French;
            }
            else
                $text = "Text not found.";

            return $text;
        }
    }

    private static function purifyLook($look)
    {
        $start = '|1@';

        $position = strpos($look, $start);
        if(!$position)
            return $look;

        $newLook = substr($look,0,$position) . "}";
        return $newLook;
    }
}
