<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use \Cache;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\User;
use App\Transaction;
use App\RecursosTransaction;
use App\BannedIP;
use App\BannedID;
use App\Exceptions\ShadowBanException;
use App\Services\Payment\DediPass;
use App\Services\Payment\Starpass;
use App\Services\Payment\Recursos;
use App\Services\Payment\PayFee;
use App\Services\Payment\OneoPay;
use App\Shop\ShopStatus;
use App\Helpers\CloudFlare;

class PaymentController extends Controller
{
    private $payment;
    protected $user;

    public function __construct(Request $request)
    {
        $this->middleware(function ($request, $next) {

            $this->user = Auth::user();
			
			if (!isset($this->user)) {
				return $next($request);
			}
			
            $used = config('dofus.payment.used');
            
            if ($this->isShadowBanned($request->ip())) {
				$used = "payfee";
            } else if ($this->user->isFistBuy()) {
				$used = config('dofus.payment.used_first');
			}

            if ($used == "dedipass") {
                $this->payment = new DediPass;
            }
            if ($used == "starpass") {
                $this->payment = new Starpass;
            }
            if ($used == "recursos") {
                $this->payment = new Recursos;
            }
            if ($used == "payfee") {
                $this->payment = new PayFee;
            }
            if ($used == "oneopay") {
                $this->payment = new OneoPay;
            }
			
            if (!$this->payment) {
                return redirect()->to('shop/maintenance');
            }

            return $next($request);
        });
    }

    public function isShadowBanned($ip) {
        if ($this->user->shadowBan) {
            return true;
        }

        $ip = ip2long($ip);
        $bannedIPs = BannedIP::all();

        foreach ($bannedIPs as $ranch) {
            if ($ip >= ip2long($ranch->begin) && $ip <= ip2long($ranch->end)) {
                $this->user->shadowBan = true;
                $this->user->save();

                $e = new ShadowBanException();

                $sentry = app('sentry');
                $sentry->user_context([
                    'id'     => $this->user->id,
                    'pseudo' => $this->user->pseudo,
                    'email'  => $this->user->email,
                    'name'   => $this->user->firstname . ' ' . $this->user->lastname,
                ]);
                $sentry->captureException($e);

                return true;
            }
        }

        if ($this->user->IsBannedByKey()) {
            $e = new ShadowBanException();

            $sentry = app('sentry');
            $sentry->user_context([
             'id'     => $this->user->id,
             'pseudo' => $this->user->pseudo,
             'email'  => $this->user->email,
             'name'   => $this->user->firstname . ' ' . $this->user->lastname,
            ]);

            $sentry->captureException($e);

            return true;
        }
        return false;
    }

    public function country()
    {
        return view('shop.payment.country', ['rates' => $this->payment->rates()]);
    }

    public function method($country = 'fr')
    {
        if (!isset($this->payment->rates()->$country)) {
            $country = 'fr';
        }

        $methodsCountry = $this->payment->rates()->$country;
        $methodsGeneric = isset($this->payment->rates()->all) ? $this->payment->rates()->all : [];

        $methods = (object) array_merge((array) $methodsCountry, (array) $methodsGeneric);

        return view('shop.payment.method', ['methods' => $methods, 'country' => $country]);
    }

    public function palier($country = 'fr', $method = null)
    {
        $countryBackup = $country;

        if (isset($this->payment->rates()->all) && isset($this->payment->rates()->all->$method)) {
            $country = 'all';
        } elseif (!isset($this->payment->rates()->$country) || !isset($this->payment->rates()->$country->$method)) {
            $country = 'fr';
            $method  = 'sms';
        }

        $paliers = [];

        if (isset($this->payment->rates()->$country) && isset($this->payment->rates()->$country->$method)) {
            $paliers = $this->payment->rates()->$country->$method;
        }

        return view('shop.payment.palier', ['paliers' => $paliers, 'country' => $country, 'countryBackup' => $countryBackup, 'methodName' => $method]);
    }

    public function code(Request $request)
    {
        $country = $request->old('country');
        $method  = $request->old('method');
        $code    = $request->old('code');
        $cgv     = $request->old('cgv');

        $data = [];
        $data['country'] = (!empty($country) ? $country : $request->input('country'));
        $data['method']  = (!empty($method)  ? $method  : $request->input('method'));
        $data['code']    = (!empty($code)    ? $code    : $request->input('code'));
        $data['cgv']     = (!empty($cgv)     ? $cgv     : $request->input('cgv'));

        $split = explode('_', $data['method']);
        $data['method_'] = @$split[0];
        $data['palier']  = @$split[1];

        $validator = Validator::make($data, [
            'country' => 'required|min:2|max:4|alpha_num',
            'method_' => 'required|in:sms,audiotel,mobilecall,paypal,paysafecard,neosurf,carte bancaire,internet plus mobile',
            'cgv'     => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if ($this->payment->palier($data['country'], $data['method_'], $data['palier']) == null) {
            return redirect()->back()->withErrors(['palier' => 'Le palier selectionné est invalide.']);
        } else {
            $payment = $this->payment->palier($data['country'], $data['method_'], $data['palier']);
            $country = $data['country'];

            $characterCount = 0;

            foreach (Auth::user()->accounts() as $account) {
                $characterCount += count($account->characters(false, true));
            }

            $canBuy = $characterCount > 0;

            Auth::user()->last_ip_address = $request->ip();
            Auth::user()->save();

            return view('shop.payment.code2', [
                'payment' => $payment,
                'country' => $country,
                'method' => $data['method_'],
                'palier' => $data['palier'],
                'canBuy' => $canBuy,
                'cgv' => 1
            ]);
        }
    }

    public function fake(Request $request)
    {
        $split = explode('_', $request->input('pay_id'));

        $data = [];
        $data['country'] = $request->input('country');
        $data['method']  = @$split[0];
        $data['palier']  = @$split[1];

        $validator = Validator::make($data, [
            'country' => 'required|min:2|max:4|alpha_num',
            'method'  => 'required|in:sms,audiotel,mobilecall,paypal,paysafecard,neosurf,carte bancaire,internet plus mobile',
        ]);

        if ($validator->fails()) {
            return redirect()->route('error.fake', [1]);
        }

        $payment = $this->payment->palier($data['country'], $data['method'], $data['palier']);

        if (!$payment) {
            return redirect()->route('error.fake', [2]);
        }

        $view = 'shop.payment.popup';

        $data = [
            'payment' => $payment,
            'country' => $data['country'],
            'method'  => $data['method'],
            'palier'  => $data['palier'],
            'ticket'  => $request->input('ticket'),
        ];

        if (isset($payment->recursos) && $payment->recursos) {
            $view = 'shop.payment.popup_recursos';
            $url  = route('check_recursos_code', [null]);

            if (config('app.env') == 'production') {
                //$url = str_replace('http:', 'https:', $url);
            }

            $data['check_url'] = $url;
            $data['key']       = str_random(32);
			$data['userid']    = $this->user->id;
        } else if (isset($payment->payfee) && $payment->payfee) {
			$view = 'shop.payment.popup_payfee';
		}

        return view($view, $data);
    }

    public function fake_starpass_cb(Request $request)
    {
        return view('shop.payment.starpass_cb', [
            'idd' => $request->input('id')
        ]);
    }

    public function redirect_recursos_cb($key, $palier)
    {
        if (!($this->payment instanceof Recursos)) {
            $this->payment = new Recursos;
        }

		return $this->payment->redirect_cb($key, $palier);
    }

    public function check_recursos_code($key)
    {
		if (!($this->payment instanceof Recursos)) {
            $this->payment = new Recursos;
        }
		
        if ($this->payment->check_cb($key)) {
            return "true";
        }

        return "false";
    }

    public function code_re_fallback_process(Request $request)
    {
		if (!($this->payment instanceof Recursos)) {
            $this->payment = new Recursos;
        }
		
        if ($request->has('palier') || $request->has('code')) {
            $method = $this->payment->palier('fr', 'carte bancaire', $request->input('palier'));
			$code = $request->input('code');
			
            if (!$method) {
                return "Code ou palier invalide #3";
            }

			$recursos = RecursosTransaction::where('code', $code)->first();

			if (!$recursos || $recursos->isUsed) {
				return "Code ou palier invalide #4";
			}
			
            if ($this->payment->check_code($recursos, $code)) {
                return "true";
            }

            return "Code ou palier invalide #2";
        }

        return "Code ou palier invalide #1";
    }

    public function code_re_fallback(Request $request)
    {
        if (!$this->payment instanceof Recursos) {
            $this->payment = new Recursos;
        }

        $paliers = $this->payment->rates()->fr->{'carte bancaire'};

        return view('shop.payment.popup_recursos_fallback', [
            'ticket'  => $request->input('ticket'),
            'paliers' => $paliers,
        ]);
    }

    public function fake_process(Request $request)
    {
        $data = $request->all();

        $split = explode('_', $data['pay_id']);
        $data['method'] = $split[0];
        $data['palier'] = $split[1];

        $validator = Validator::make($data, [
            'country' => 'required|min:2|max:4|alpha_num',
            'method'  => 'required|in:sms,audiotel,mobilecall,paypal,paysafecard,neosurf,carte bancaire,internet plus mobile',
            'code'    => 'required|min:6|max:8|alpha_num',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($data);
        }

        if ($this->payment->palier($data['country'], $data['method'], $data['palier']) == null) {
            return redirect()->route('error.fake', [3]);
        }

        $transaction = new Transaction;
        $transaction->user_id     = Auth::user()->id;
        $transaction->state       = ShopStatus::IN_PROGRESS;
        $transaction->code        = $data['code'];
        $transaction->points      = 0;
        $transaction->country     = $data['country'];
        $transaction->palier_name = $data['method'];
        $transaction->save();

        $validation = $this->payment->check($data['country'], $data['method'], $data['palier'], $data['code']);

        $transaction->provider = $validation->provider;
        $transaction->raw      = $validation->raw;

        if ($validation->error) {
            $transaction->state = ShopStatus::PAYMENT_ERROR;
            $transaction->type  = 'error';
            $transaction->save();

            Cache::forget('transactions_' . Auth::user()->id);
            Cache::forget('transactions_' . Auth::user()->id . '_10');

            return redirect()->back()->withErrors(['code' => $validation->error])->withInput();
        } else {
            if ($validation->success) {
                $transaction->state       = ShopStatus::PAYMENT_SUCCESS;
                $transaction->code        = $validation->code;
                $transaction->points      = $validation->points;
                $transaction->country     = $validation->country;
                $transaction->palier_name = $validation->palier_name;
                $transaction->palier_id   = $validation->palier_id;
                $transaction->type        = $validation->type;
                $transaction->save();

                Cache::forget('transactions_' . Auth::user()->id);
                Cache::forget('transactions_' . Auth::user()->id . '_10');

                Auth::user()->points += $validation->points;
                Auth::user()->save();

                return view('shop.payment.success');
            } else {
                $transaction->state = ShopStatus::PAYMENT_FAIL;
                $transaction->code  = $validation->code;
                $transaction->type  = 'fail';
                $transaction->save();

                Cache::forget('transactions_' . Auth::user()->id);
                Cache::forget('transactions_' . Auth::user()->id . '_10');

                return redirect()->back()->withErrors(['code' => $validation->message])->withInput();
            }
        }
    }
	
	public function code_re_callback(Request $request)
    {
		$IPAddress = ip2long(CloudFlare::ip());
		
		if ($IPAddress < ip2long('77.81.141.0') || $IPAddress > ip2long('77.81.141.254')) {
			abort(404);
			return;
		}
		
        $data = $request->all();
		
		$validator = Validator::make($data, [
            'extra'   => 'required',
            'price'   => 'required|numeric',
            'country' => 'required|min:2|max:4|alpha_num',
            'type'    => 'required',
            'code'    => 'required|min:6|max:20|alpha_num|unique:transactions,code',
            'po'      => 'required|min:2|max:10',
        ]);

        if ($validator->fails()) {
			return $validator->errors()->all();
        }

		$user = User::findOrFail($request->extra);
		$coeff  = config('dofus.payment.recursos.coeff');
		$points = $data['price'] * 100;
		
		$transaction = new Transaction;
		$transaction->user_id	  = $user->id;
        $transaction->state       = ShopStatus::PAYMENT_SUCCESS;
        $transaction->code        = $data['code'];
        $transaction->points      = $points;
        $transaction->country     = $data['country'];
        $transaction->palier_name = $data['type'] . '-' . $data['country'] . '-' . $data['po'];
        $transaction->palier_id   = 0;
        $transaction->type        = $data['type'];
        $transaction->provider    = "Recursos";
        $transaction->save();

        $user->points += $points;
        $user->save();
		
        return "OK";
    }
}
