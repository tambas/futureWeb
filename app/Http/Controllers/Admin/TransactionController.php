<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Shop\ShopStatus;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class TransactionController extends Controller
{
    public function index()
    {
        return view('admin.transactions.index');
    }

    public function getData()
    {
        $days = [];
        for ($i = 30; $i >= 0; $i--) {
            $days[] = [
                'day' => Carbon::today()->subDay($i)->toDateString(),
                'earn' => Transaction::GetDayEarnings(Carbon::today()->subDay($i)->toDateString())
            ];
        }
        $days_json = json_encode($days);
        return response()->json($days_json, 202);
    }
}
