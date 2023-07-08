<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Experience;
use \Cache;
use App\Services\DofusForge;

class Title extends Model
{
   protected $table = 'tinsel_titles';

   public function name()
   {
        return DofusForge::text($this->NameId, $this->server);
   }
}
