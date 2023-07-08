<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoteReward extends Model
{
    protected $table = 'vote_rewards';

    public $timestamps = false;

    public function item()
    {
        return $this->hasOne(ItemTemplate::class, 'Id', 'itemId');
    }
}
