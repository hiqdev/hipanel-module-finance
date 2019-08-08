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
use Yii;

/**
 * Class CurrencyFilter can be used to filter currencies array in finance module
 */
class CurrencyFilter
{
    /**
     * Adds currency symbols to each of element and filters it
     * @param string[] $currencies Need to filter currencies list
     * @return string[] Filtered currencies list with symbols
     */
    public static function addSymbolAndFilter(array $currencies): array
    {
        $currencies = array_combine(array_keys($currencies), array_map(function (string $k) {
            return StringHelper::getCurrencySymbol($k);
        }, array_keys($currencies)));

        return static::getUsedCurrencies($currencies);
    }

    /**
     * Filters input array of currencies
     * @param string[] $currencies
     * @return string[]
     */
    private static function getUsedCurrencies(array $currencies): array
    {
        $filterCurrencies = Yii::$app->cache->getOrSet([__METHOD__, Yii::$app->user->id], function () {
            return ArrayHelper::getColumn(Bill::perform('get-used-currencies', [], ['batch' => true]), 'name');
        }, 3600);

        return array_filter($currencies, function (string $cur) use ($filterCurrencies) {
            return in_array($cur, $filterCurrencies);
        }, ARRAY_FILTER_USE_KEY);
    }
}
