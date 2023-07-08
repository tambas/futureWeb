<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    public function item()
    {
        return ItemTemplate::on($this->server . '_world')->where('id', $this->item_id)->first();
    }
}
