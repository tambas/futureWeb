<?php

namespace App\Services\Payment;

use \Cache;
use App\Services\Payment;
use Auth;

class DediPass extends Payment
{
    private $rates;

    const CACHE_EXPIRE_MINUTES = 1440; // 1 day

    public function __construct()
    {
        $json = null;

        if (Cache::has('payment.dedipass')) {
            $json = Cache::get('payment.dedipass');
        } else {
            $url = config('dofus.payment.dedipass.url');
            $url = str_replace('{PUBLIC_KEY}', config('dofus.payment.dedipass.public'), $url);
            $json = json_decode(file_get_contents($url));

            Cache::add('payment.dedipass', $json, self::CACHE_EXPIRE_MINUTES);
        }

        $this->rates = new \stdClass;

        if ($json) {
            foreach ($json as $method) {
                $countryName = strtolower($method->country->iso);
                $methodName  = strtolower($method->solution);
                $palier      = $method->rate;

                if (!property_exists($this->rates, $countryName)) {
                    $this->rates->$countryName = new \stdClass;
                }

                if (!property_exists($this->rates->$countryName, $methodName)) {
                    $this->rates->$countryName->$methodName = new \stdClass;
                }

                $newMethod = new \stdClass;

                $newMethod->devise = $method->user_currency == "EUR" ? "&euro;" : $method->user_currency;
                $newMethod->text   = $method->mention;
                $newMethod->cost   = $method->user_price . " " . $newMethod->devise;
                $newMethod->points = $method->user_earns;
                $newMethod->link   = $method->link;

                if ($methodName == "sms") {
                    $newMethod->number  = $method->shortcode;
                    $newMethod->keyword = $method->keyword;
                }

                if ($methodName == "audiotel") {
                    $newMethod->number = $method->phone;
                }

                if (property_exists($method, 'legal_graphic')) {
                    $newMethod->legal = $method->legal_graphic;
                }

                $this->rates->$countryName->$methodName->$palier = $newMethod;
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
        $check = new \stdClass;
        $check->code = $code;
        $check->error = false;

        $public     = config('dofus.payment.dedipass.public');
        $private    = config('dofus.payment.dedipass.private');
        $validation = config('dofus.payment.dedipass.validation');

        $validation = str_replace('{PUBLIC_KEY}', $public, $validation);
        $validation = str_replace('{PRVIATE_KEY}', $private, $validation);
        $validation = str_replace('{PALIER}', $palier, $validation);
        $validation = str_replace('{CODE}', $code, $validation);

        $check->provider = config('dofus.payment.dedipass.name');

        //$json   = @file_get_contents($validation);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $validation);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36');

        $json = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($json);

        $check->raw = $json;

        if (isset($result->status) && $result->status == "success") {
            $check->success = true;

            $identifier = explode('-', $result->identifier);

            if ($result->identifier == 'TEST-CODE') {
                if (Auth::user()->isAdmin()) {
                    $check->country = 'xx';
                    $check->type    = 'test';
                } else {
                    $check->success = false;
                    $check->message = "Rang insuffisant pour utiliser le code de test";
                    return $check;
                }
            } else {
                if (count($identifier) >= 3) {
                    $check->country = strtolower($identifier[1]);
                    $check->type    = strtolower($identifier[2]);
                } else {
                    $check->country = 'xx';
                    $check->type    = strtolower($identifier[1]);
                }
            }

            $check->palier_name = $result->identifier;
            $check->palier_id   = 0;
            $check->points      = $result->virtual_currency;
            $check->message     = $result->message;
            $check->payout      = $result->payout;
        } else {
            $check->message = isset($result->message) ? $result->message : "Aucune réponse du serveur de validation";
            $check->success = false;
        }

        return $check;
    }
}
