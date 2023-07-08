<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ItemTemplate;
use App\Lottery;
use App\LotteryItem;
use App\Shop\ShopStatus;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Resource\Item;

class LotteryItemController extends Controller
{
    private function isLotteryExist($lotteryId)
    {
        if (Lottery::findOrFail($lotteryId)) {
            return true;
        }
        return false;
    }

    private function isItemExist($itemid, $server = 0)
    {
        $item = ItemTemplate::on(config('dofus.servers')[$server].'_world')->where('Id', $itemid)->first();
        if ($item) {
            return true;
        }
        return false;
    }

    public function index(Lottery $lottery)
    {
        $type = Lottery::findOrFail($lottery->id);
        $serversArray = config('dofus.servers');
        $servers = [];
        foreach ($serversArray as $k => $server) {
            $servers[$k] = ucfirst($server);
        }
        return view('admin.lottery.item.index', compact('type', 'servers'));
    }

    public function getItemData(Request $request, Lottery $lottery, $server, $itemid)
    {
        $validation = ['itemid' => $itemid];
        $validator = Validator::make($validation, ItemTemplate::$rules['getItemById']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        if (!$this->isItemExist($itemid, $server)) {
            return response()->json(['itemid' => ['0' => 'Item not found']], 422);
        }

        $server = config('dofus.servers')[$server];

        $item = ItemTemplate::on($server . '_world')->where('Id', $itemid)->first();
        $item_image = $item->image();
        $item_name  = $item->name($server);
        $item_array = ['image' => $item_image, 'name' => $item_name];
        return response()->json(json_encode($item_array), 202);
    }

    public function store(Request $request, Lottery $lottery)
    {
        if (!$this->isLotteryExist($lottery->id)) {
            abort(404);
        }

        $validator = Validator::make($request->all(), LotteryItem::$rules['store']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        if (!$this->isItemExist($request->item, $request->input('server'))) {
            return response()->json(['item' => ['0' => 'This item doesn\'t exist']], 422);
        }

        $item = new LotteryItem;
        $item->type       = $lottery->type;
        $item->percentage = $request->percentage;
        $item->item_id    = $request->item;
        $item->max        = $request->input('max') ? true : false;
        $item->server     = @config('dofus.servers')[$request->input('server')];
        $item->save();

        return response()->json([], 202);
    }

    public function destroy(Request $request, Lottery $lottery, $itemid)
    {
        if (!$this->isLotteryExist($lottery->id)) {
            abort(404);
        }
        $item = LotteryItem::findOrFail($itemid);
        $item->delete();
        return response()->json([], 200);
    }

    public function update(Request $request, Lottery $lottery, $itemid)
    {
        if (!$this->isLotteryExist($lottery->id)) {
            abort(404);
        }

        $validator = Validator::make($request->all(), LotteryItem::$rules['update']);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $item = LotteryItem::findOrFail($itemid);
        $item->percentage = $request->percentage;
        $item->max        = $request->input('max') ? true : false;
        $item->save();

        return response()->json([$request->percentage], 202);
    }
}
