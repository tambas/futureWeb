<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ModelCustom;
use Cache;
use App\Character;

class MarketCharacter extends Model
{
    protected $table = 'market_characters';

    protected $dates = ['buy_date'];

    public function character()
    {
         $character = Cache::remember('character_view_'.$this->server.'_'.$this->character_id, 120, function () {
            return ModelCustom::hasOneOnOneServer('world', $this->server, Character::class, 'Id', $this->character_id);
         });
         $character->server = $this->server;
         return $character;
    }

    public static function inSell(Character $character)
    {
        return MarketCharacter::where('character_id', $character->Id)->where('buy_date', null)->first();
    }

    public function scopeInsell($query)
    {
        $query->where('buy_date', null);
    }

    public function scopeSold($query)
    {
        $query->where('buy_date', '!=', null);
    }

    public function scopeBuyed($query, $user_id)
    {
        $query->where('buy_date', '!=', null)->where('buyer_id', $user_id);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
