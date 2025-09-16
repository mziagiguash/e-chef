<?php

namespace App\Http\Helpers;

class CurrencyHelper
{
    public static function getSymbol()
    {
        // Можно получить из конфига, базы данных или других источников
        return config('app.currency_symbol', '₾');
    }
}
