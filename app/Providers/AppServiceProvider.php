<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $locale = app()->getLocale(); // уже установлено в middleware
        $currencyConfig = config("currency.$locale", config('currency.en'));

        View::share('currentCurrency', $currencyConfig['symbol']);
        View::share('currencyRate', $currencyConfig['rate']);
    }
}
