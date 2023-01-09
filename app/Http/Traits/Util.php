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

    /**
     * Gives a list of all timezone
     *
     * @return array
     */
    public static function allTimeZones()
    {
        $datetime = new \DateTimeZone("EDT");

        $timezones = $datetime->listIdentifiers();
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[$timezone] = $timezone;
        }

        return $timezone_list;
    }

    /**
     * get all monthes
     * 
     * @return array
     */
    public static function getAllMonthes()
    {
        // $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }
        return $months;
    }

    /**
     * Gives a list of all accouting methods
     *
     * @return array
     */
    public static function allAccountingMethods()
    {
        return [
            'fifo' => __('business.fifo'),
            'lifo' => __('business.lifo')
        ];
    }
}
