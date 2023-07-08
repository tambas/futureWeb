<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Cache;
use App\Services\DofusForge;

class Characterspell extends Model
{
    protected $primaryKey = 'Id';

    protected $table = 'characters_spells';

    public $timestamps = false;

    public $server;
    
    public function character()
    {
        return $this->hasOne(Character::class, 'Id', 'OwnerId')->first();
    }
    public function template($server)
    {
        $template =  Cache::remember('spell_template_'.$server.'_'.$this->SpellId, 5000, function () use($server) {
               return ModelCustom::hasOneOnOneServer('world', $server, SpellTemplate::class, 'Id', $this->SpellId);
        });
       return $template ? $template : null;
    }
}
