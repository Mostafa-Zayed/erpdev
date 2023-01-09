<?php

namespace App\Http\Traits;
use App\Currency;
use \DB;

trait Util
{
    /**
     * Gives a list of all currencies
     *
     * @return array
     */
    public static function allCurrencies()
    {
        $currencies = Currency::select('id', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
                ->orderBy('country')
                ->pluck('info', 'id');

        return $currencies;
    }
}