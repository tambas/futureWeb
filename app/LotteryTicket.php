<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Lottery;
use App\LotteryItem;
use App\ItemTemplate;

class LotteryTicket extends Model
{
    const NORMAL = 0;
    const GOLD   = 1;
    const NOWEL  = 2;

    public function objects($server)
    {
        return $this->hasMany(LotteryItem::class, 'type', 'type')->where('server', $server)->get();
    }

    public function draw($server)
    {
        $objects = $this->objects($server);
        $max = 0;

        foreach ($objects as $object) {
            $max += $object->percentage;
        }

        $random = rand(1, $max);
        $percentage = 1;

        foreach ($objects as $object) {
            $current = $percentage;
            $percentage += $object->percentage;

            if ($random >= $current && $random < $percentage) {
                return $object;
            }
        }
    }

    public function item($server = null)
    {
        if ($this->item_id) {
            if ($server) {
                return ItemTemplate::on($server . '_world')->where('id', $this->item_id)->first();
            } else {
                return $this->hasOne(ItemTemplate::class, 'Id', 'item_id')->first();
            }
        }

        return null;
    }

    public function lottery()
    {
        return $this->hasOne(Lottery::class, 'type', 'type')->first();
    }

    public function giver()
    {
        if ($this->giver) {
            $giver = User::find($this->giver);
            return $giver ? $giver->pseudo : null;
        }
        return null;
    }
}
