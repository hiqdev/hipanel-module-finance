<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\helpers;

use hipanel\helpers\ArrayHelper;
use hipanel\helpers\StringHelper;
use hipanel\modules\finance\models\Bill;

/**
 * Class CurrencyFilter can be used to filter currencies array in finance module
 */
class CurrencyFilter
{
    /**
     * Adds currency symbols to each of element and filters it
     * @param array $currencies Need to filter currencies list
     * @return array Filtered currencies list
     */
    public static function addSymbolAndFilter(array $currencies): array
    {
        $currencies = array_combine(array_keys($currencies), array_map(function ($k) {
            return StringHelper::getCurrencySymbol($k);
        }, array_keys($currencies)));

        return static::getUsedCurrencies($currencies);
    }

    /**
     * Filters input array of currencies
     * @param array $currencies
     * @return array
     */
    private static function getUsedCurrencies(array $currencies): array
    {
        $filterCurrencies = ArrayHelper::getColumn(Bill::perform('get-used-currencies', [], ['batch' => true]), 'name');

        return array_filter($currencies, function ($cur) use ($filterCurrencies) {
            return in_array($cur, $filterCurrencies);
        }, ARRAY_FILTER_USE_KEY);
    }
}
