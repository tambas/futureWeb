<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;
class Experience extends Model
{
    protected $table = 'experiences';

    protected $connection = 'sigma_world';

    public static function maxExp($server)
    {
        $maxExp = Cache::remember('exp_' . $server . '_max', 1440, function () use($server){
                return Experience::on($server . '_world')->orderBy('CharacterExp', 'desc')->first();
            });

        return $maxExp->CharacterExp;
    }
}
