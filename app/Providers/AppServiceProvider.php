<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * @var $validator Validator
         */
        Validator::extend('custom_numeric', function ($attribute, $value, $parameters, $validator) {

            if (!preg_match('/^[0-9]{1,11}[.,]?[0-9]{0,4}$/i', $value)) {
                return false;
            }
            $value = floatval($value);
            if (($value < (int)$parameters[0]) OR ($value > (int)$parameters[1])) {
                return false;
            }
            return true;
        });
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
