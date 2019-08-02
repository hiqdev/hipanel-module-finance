<?php


namespace hipanel\modules\finance\helpers;


use hipanel\helpers\ArrayHelper;
use hipanel\helpers\StringHelper;
use hipanel\modules\finance\models\Bill;

class CurrencyFilter
{
    public static function filter($currencies)
    {
        $currencies = array_combine(array_keys($currencies), array_map(function ($k) {
            return StringHelper::getCurrencySymbol($k);
        }, array_keys($currencies)));

        return static::getUsedCurrencies($currencies);
    }


    private static function getUsedCurrencies($currencies): array
    {
        $filterCurrencies = ArrayHelper::getColumn(Bill::perform('get-used-currencies', [], ['batch' => true]), 'name');

        return array_filter($currencies, function ($cur) use ($filterCurrencies) {
            return in_array($cur, $filterCurrencies);
        }, ARRAY_FILTER_USE_KEY);
    }
}
