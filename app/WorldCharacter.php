<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Cache;

use App\Character;

class WorldCharacter extends Model
{
    protected $primaryKey = 'CharacterId';


    protected $table = 'worlds_characters';

    public $server;

    public $timestamps = false;
    
    public function character()
    {
        return ModelCustom::hasOneOnOneServer('world', $this->server, Character::class, 'Id', $this->CharacterId);
    }

    public function account()
    {
        return ModelCustom::hasOneOnOneServer('auth', $this->server, Account::class, 'Id', $this->AccountId);
    }
}
