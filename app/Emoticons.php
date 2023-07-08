<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Experience;
use \Cache;
use App\Services\DofusForge;

class Emoticons extends Model
{
   protected $table = 'Emoticons';

   public $server;

    public function name()
    {
        return DofusForge::text($this->NameId, $this->server);
    }
}
