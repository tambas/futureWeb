<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lottery;
use App\Shop\ShopStatus;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Kamaln7\Toastr\Facades\Toastr;

class LotteryController extends Controller
{
    public function tickets()
    {
        return view('admin.lottery.tickets');
    }

    public function index()
    {
        $lotteryTypes = Cache::remember('lottery_admin', 10, function () {
            return Lottery::all();
        });

        return view('admin.lottery.index', compact('lotteryTypes'));
    }

    public function edit(Lottery $lottery)
    {
        $type = Lottery::findOrFail($lottery->id);
        return view('admin.lottery.edit', compact('type'));
    }

    public function update(Request $request, Lottery $lottery)
    {
        $rules = [
            'name'               => 'required|min:3|max:30|unique:lottery,name,' . $lottery->id,
            'icon_path'          => 'image|mimes:png|max:3000',
            'image_path'         => 'image|mimes:png|max:3000'
        ];
    
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lottery = Lottery::findOrFail($lottery->id);
        $lottery->name = $request->name;

        if ($request->hasFile('icon_path')) {
            $file = $request->file('icon_path');

            $image_name = "ticket_".$file->getClientOriginalName();

            File::delete($lottery->icon_path);

            $file->move('imgs/lottery', $image_name);

            Image::make(sprintf('imgs/lottery/%s', $image_name))->resize(200, 200)->save();
            $lottery->icon_path = 'imgs/lottery/'.$image_name;
        }
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');

            $image_name = "box_".$file->getClientOriginalName();

            File::delete($lottery->image_path);

            $file->move('imgs/lottery', $image_name);

            Image::make(sprintf('imgs/lottery/%s', $image_name))->resize(200, 200)->save();
            $lottery->image_path = 'imgs/lottery/'.$image_name;
        }
        $lottery->save();

        Cache::forget('lottery_admin');
        Toastr::success('Lottery updated', $title = null, $options = []);

        return redirect()->route('admin.lottery');
    }

    public function create()
    {
        return view('admin.lottery.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name'               => 'required|min:3|max:30|unique:lottery,name',
            'icon_path'          => 'required|image|mimes:png|max:3000',
            'image_path'         => 'required|image|mimes:png|max:3000'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $maxid = Lottery::select('type')->max('type');

        $lottery = new Lottery;
        $lottery->name = $request->name;
        $lottery->type = $maxid + 1;

        $file_icon = $request->file('icon_path');
        $file_image = $request->file('image_path');

        $icon_name = "ticket_".$file_icon->getClientOriginalName();
        $image_name = "box_".$file_image->getClientOriginalName();

        $file_icon->move('imgs/lottery', $icon_name);
        $file_image->move('imgs/lottery', $image_name);

        Image::make(sprintf('imgs/lottery/%s', $icon_name))->resize(200, 200)->save();
        $lottery->icon_path = 'imgs/lottery/'.$icon_name;
        Image::make(sprintf('imgs/lottery/%s', $image_name))->resize(200, 200)->save();
        $lottery->image_path = 'imgs/lottery/'.$image_name;

        $lottery->save();

        Cache::forget('lottery_admin');
        Toastr::success('Lottery created!', $title = null, $options = []);

        return redirect()->route('admin.lottery');
    }
}
