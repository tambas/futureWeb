<?php

namespace App\Services\Payment;

use \Cache;
use App\Services\Payment;
use Auth;

use App\Transaction;
use App\RecursosTransaction;
use App\Shop\ShopStatus;

class Recursos extends Payment
{
    private $rates;

    const CACHE_EXPIRE_MINUTES = 1440; // 1 day

    public function __construct()
    {
        $this->rates = new \stdClass;
        $countryName   = 'fr';
        $this->rates->$countryName = new \stdClass;

        $pricesCB = explode('|', config('dofus.payment.recursos.pricesCB'));
        $pricesPS = explode('|', config('dofus.payment.recursos.pricesPS'));
        $coeffCB  = config('dofus.payment.recursos.coeffCB');
        $coeffPS  = config('dofus.payment.recursos.coeffPS');
		
		$cid = config('dofus.payment.recursos.c_sms');
		$wmid = config('dofus.payment.recursos.w');
			
        if ($pricesCB) {
			$methodName = 'carte bancaire';

            $this->rates->$countryName->$methodName = new \stdClass;
			
			foreach ($pricesCB as $price) {
				$Method = new \stdClass;
				
				$Method->devise   = "&euro;";
				$Method->points   = $price * $coeffCB;
				$Method->price    = $price;
				$Method->cost     = $price . " " . $Method->devise;
				$Method->text     = "";
				$Method->link     = "https://iframes.recursosmoviles.com/v3/?wmid=$wmid&cid=$cid&c=fr&m=creditcard&h=paysafecard&pcreditcard=240 Ogrines,280 Ogrines,640 Ogrines,800 Ogrines,2000 Ogrines,4000 Ogrines,6000 Ogrines,8000 Ogrines";
				$Method->recursos = true;

				if (config('app.env') == 'production') {
					//$Method->link = str_replace('http:', 'https:', $Method->link);
				}

				$Method->legal = new \stdClass;
				$Method->legal->header    = null;
				$Method->legal->phone     = null;
				$Method->legal->shortcode = null;
				$Method->legal->keyword   = null;
				$Method->legal->footer    = null;		
				
				$palier = substr(md5($Method->cost), 0, 5);
				$this->rates->$countryName->$methodName->$palier = $Method;
			}
		}
		
		if ($pricesPS) {
            $methodName = 'paysafecard';

            $this->rates->$countryName->$methodName = new \stdClass;
			
			foreach ($pricesPS as $price) {
				$Method = new \stdClass;
				
				$Method->devise   = "&euro;";
				$Method->points   = $price * $coeffPS;
				$Method->price    = $price;
				$Method->cost     = $price . " " . $Method->devise;
				$Method->text     = "";
				$Method->link     = "https://iframes.recursosmoviles.com/v3/?wmid=$wmid&cid=$cid&c=fr&m=paysafecard&&h=creditcard&ppaysafecard=210 Ogrines,245 Ogrines,560 Ogrines,700 Ogrines,1750 Ogrines,3500 Ogrines,5250 Ogrines,7000 Ogrines";
				$Method->recursos = true;

				if (config('app.env') == 'production') {
					//$Method->link = str_replace('http:', 'https:', $Method->link);
				}

				$Method->legal = new \stdClass;
				$Method->legal->header    = null;
				$Method->legal->phone     = null;
				$Method->legal->shortcode = null;
				$Method->legal->keyword   = null;
				$Method->legal->footer    = null;	
				
				$palier = substr(md5($Method->cost), 0, 5);
				$this->rates->$countryName->$methodName->$palier = $Method;
			}		
		}
    }

    public function rates()
    {
        return $this->rates;
    }

    public function palier($country, $method, $palier)
    {
        if (property_exists($this->rates, $country) &&
            property_exists($this->rates->$country, $method) &&
            property_exists($this->rates->$country->$method, $palier)) {
            return $this->rates->$country->$method->$palier;
        }

        return null;
    }

    public function check($country, $method, $palier, $code)
    {
    }

    public function redirect_cb($key = null, $palier = null)
    {
        if (!$key || !$palier) {
            return redirect()->route('error.fake', [6]);
        }

        $method = $this->palier('fr', 'carte bancaire', $palier);

        $params = [
            't'     => 'creditcard',
            'p'     => $method->price,
            'co'    => 'fr',
            'c'     => config('dofus.payment.recursos.c'),
            'w'     => config('dofus.payment.recursos.w'),
            'email' => Auth::user()->email,
        ];

        $c = curl_init("https://iframes.recursosmoviles.com/v3/redirect.php?id=$key");
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_REFERER, 'https://iframes.recursosmoviles.com');
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        $page = curl_exec($c);
        curl_close($c);

		$c = curl_init("https://iframes.recursosmoviles.com/v3/checkid.php?id=$key");
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_REFERER, 'https://iframes.recursosmoviles.com');
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		$code = curl_exec($c);
		curl_close($c);
			
		if (!$page || !$code) {
			return redirect()->route('error.fake', [10]);
		}
		
        $recursos = new RecursosTransaction;
        $recursos->user_id = Auth::user()->id;
        $recursos->key     = $key;
		$recursos->code	   = $code;
        $recursos->points  = $method->points;
        $recursos->price   = $method->price;
        $recursos->save();

        return $page;
    }

    public function check_cb($key)
    {
        $recursos = RecursosTransaction::where('key', $key)->first();

        if (!$recursos || $recursos->isUsed) {
            return false;
        }

		if (!$recursos->code) {
			$c = curl_init("https://iframes.recursosmoviles.com/v3/checkid.php?id=$key");
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_REFERER, 'https://iframes.recursosmoviles.com');
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			$code = curl_exec($c);
			curl_close($c);

			$recursos->code = $code;
			$recursos->save();		
		}
		
        return $this->check_code($recursos, $recursos->code);
    }
	
    public function check_code($recursos, $code)
    {
        $params = [
            't'     => 'creditcard',
            'p'     => $recursos->price,
            'co'    => 'fr',
            'c'     => config('dofus.payment.recursos.c'),
            'w'     => config('dofus.payment.recursos.w'),
            'code'  => $code,
        ];

        $c = curl_init("https://iframes.recursosmoviles.com/v3/checkcode.php");
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_REFERER, 'https://iframes.recursosmoviles.com');
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($c);
        curl_close($c);

        $data = explode(':', $result);

        if ($data[0] == "OK" || (strpos($data[1], '(004)') !== false && !Transaction::where('code', $code)->first())) {
            $recursos->isUsed = true;
            $recursos->save();

            $transaction = new Transaction;
            $transaction->user_id     = Auth::user()->id;
            $transaction->state       = ShopStatus::PAYMENT_SUCCESS;
            $transaction->code        = $code;
            $transaction->points      = $recursos->points;
            $transaction->country     = "all";
            $transaction->palier_name = "-";
            $transaction->palier_id   = 0;
            $transaction->type        = "carte bancaire";
            $transaction->provider    = "Recursos";
            $transaction->raw         = $result;
            $transaction->save();

            Cache::forget('transactions_' . Auth::user()->id);
            Cache::forget('transactions_' . Auth::user()->id . '_10');

            Auth::user()->points += $recursos->points;
            Auth::user()->save();

            return true;
        }

        return false;
    }
}
