<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use \Cache;
use Validator;
use App\User;
use App\MarketCharacter;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('breedarray', function ($attribute, $value, $parameters)
        {
            $result = true;
            foreach ($value as $v) {
                if (!in_array($v, [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18])) {
                    $result = false;
                }
            }
            return $result;
        });

        Validator::extend('serverarray', function ($attribute, $value, $parameters)
         {
            $result = true;
            if(is_array($value))
            {
                foreach ($value as $v) {
                    if (!in_array($v, config('dofus.servers'))) {
                        $result = false;
                    }
                }
            }
            else
            {
                if (!in_array($value, config('dofus.servers'))) {
                        $result = false;
                    }
            }

            return $result;
        });
        

        Validator::extend('sexarray', function ($attribute, $value, $parameters)
         {
            $result = true;
            foreach ($value as $v) {
                if (!in_array($v, [0,1])) {
                    $result = false;
                }
            }
            return $result;
        });

        Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
            $password = $parameters[0];
            $salt     = $parameters[1];
            return $password === User::hashPassword($value, $salt);
        });

        Validator::extend('old_passwordStump', function ($attribute, $value, $parameters, $validator) {
            $password = $parameters[0];
            return $password === md5($value);
        });

        Blade::extend(function ($value) {
            return preg_replace('/\{\?(.+)\?\}/', '<?php ${1} ?>', $value);
        });

        Carbon::setlocale(config('app.locale'));
        setlocale(LC_TIME, config('app.locale'));


        $shopCharacters = Cache::remember('shopcharacters', 60, function () {
            return MarketCharacter::latest('created_at')->insell()->take(20)->get();
        });
        
        View::share('shopCharacter', $shopCharacters->random(1));


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
