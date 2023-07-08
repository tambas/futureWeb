<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\ItemTemplate;
use App\Lottery;

class LotteryItem extends Model
{
    public static $rules = [
        'store' => [
            'item'       => 'required|numeric|digits_between:1,5',
            'percentage' => 'required|numeric|between:1,100|integer',
            'server'     => 'required'
        ],
        'update' => [
            'percentage' => 'required|numeric|between:1,100|integer'
        ],
    ];

    public function item($server = null)
    {
        if ($server) {
            return ItemTemplate::on($server . '_world')->where('id', $this->item_id)->first();
        } else {
            return $this->hasOne(ItemTemplate::class, 'Id', 'item_id')->first();
        }
    }

    public function lottery()
    {
        return $this->hasOne(Lottery::class, 'type', 'type')->first();
    }
}
