<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    const IN_PROGRESS = 0;
    const OK_API      = 1;
    const OK_SQL      = 2;
    const FAIL        = 3;
    const REFUND      = 4;

    public function item()
    {
        return ItemTemplate::on($this->server . '_world')->where('id', $this->type)->first();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->first();
    }
}
