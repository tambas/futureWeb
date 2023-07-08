<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharacterItem extends Model
{
    protected $primaryKey = 'Id';

    protected $table = 'characters_items';

    public $timestamps = false;

    public $server;
    
    public function character()
    {
        return $this->hasOne(Character::class, 'Id', 'OwnerId')->first();
    }
}
