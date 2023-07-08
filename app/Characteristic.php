<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Experience;
use \Cache;
use App\Services\DofusForge;

class Characteristic extends Model
{
   protected $table = 'characteristics';
}
