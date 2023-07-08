<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Services\DofusForge;

class SpellTemplate extends Model
{
    protected $table = 'spells_templates';

    public $timestamps = false;

    public $server;

    public function name()
    {
        return DofusForge::text($this->NameId, $this->server);
    }

    public function description()
    {
        return DofusForge::text($this->DescriptionId, $this->server);
    }

    public function image($size)
    {
        return DofusForge::asset('dofus/www/game/spells/'.$size.'/sort_' . $this->IconId . '.png');
    }
}
