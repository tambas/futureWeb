<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Cache;

use App\Experience;
use App\GuildMember;
use App\Services\DofusForge;

class Guild extends Model
{
    protected $primaryKey = 'Id';

    protected $table = 'guilds';

    public $timestamps = false;

    public $server;

    public function level()
    {
        $exp = Cache::remember('exp_'.$this->Experience, 1000, function () {
            return Experience::where('GuildExp', '<=', $this->Experience)->orderBy('Level', 'desc')->first();
        });

        if ($exp) {
            return $exp->Level;
        } else {
            return 1;
        }
    }

    public function members($server)
    {
        $guildMembers = GuildMember::on($server.'_world')->where('GuildId', $this->Id);
        return $guildMembers;
    }

    public function emblem($sizeX, $sizeY)
    {
        return DofusForge::asset("dofus/renderer/emblem/{$this->EmblemForegroundShape}/{$this->EmblemBackgroundShape}/0x".strtoupper(dechex($this->EmblemForegroundColor))."/0x".strtoupper(dechex($this->EmblemBackgroundColor))."/".$sizeX."_".$sizeY."-0.png");
    }

    public function perceptor($mode, $orientation, $sizeX, $sizeY, $margin = 0)
    {
        $baselook = "{714|".$this->emblemfetch($this->server)->SkinId."|8=#".strtoupper(dechex($this->EmblemForegroundColor)).",7=#".strtoupper(dechex($this->EmblemBackgroundColor))."|110}";
        $look = bin2hex($baselook);

        return DofusForge::asset("dofus/renderer/look/$look/$mode/$orientation/{$sizeX}_{$sizeY}-{$margin}.png");
    }

    public function emblemfetch($server)
    {
        return ModelCustom::hasOneOnOneServer('world', $server, GuildEmblem::class, 'Id', $this->EmblemForegroundShape);
    }
}
