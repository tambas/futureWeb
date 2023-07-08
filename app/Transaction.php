<?php

namespace App;

use App\Shop\ShopStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'state',
        'code',
        'points',
        'country',
        'palier_name',
        'palier_id',
        'type',
    ];

    public static function GetDayEarnings($date, $format = null)
    {
        $earn = Transaction::where('state', ShopStatus::PAYMENT_SUCCESS)->whereDate('created_at', '=', $date)->sum('points');

        if ($format) {
            return number_format($earn / 100, 2, $format, ' ');
        } else {
            return $earn / 100;
        }
    }
}
