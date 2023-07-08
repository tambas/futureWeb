<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Form;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::macro('datetimelocal', function ($name, $default = null, $attrs = []) {
            $item = '<input type="date" name="'. $name .'" ';

            if ($default) {
                $item .= 'value="'. $default .'" ';
            }

            if (is_array($attrs)) {
                foreach ($attrs as $a => $v) {
                    $item .= $a .'="'. $v .'" ';
                }
            }
            $item .= ">";

            return $item;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
