<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    protected $table = 'lottery';

    public static function fetchTicketsType()
    {
        $tickets_type = Lottery::all();
        $ticketsArray = [];
        if ($tickets_type) {
            foreach ($tickets_type as $ticket_type) {
                $ticketsArray[$ticket_type['type']] = $ticket_type['name'];
            }
        }
        return $ticketsArray;
    }

    public function objects($server)
    {
        return $this->hasMany(LotteryItem::class, 'type', 'type')->where('server', $server)->get();
    }
}
