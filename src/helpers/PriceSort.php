<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Price;
use Tuck\Sort\Sort;
use Tuck\Sort\SortChain;

/**
 * Class PriceSort provides sorting functions for prices.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceSort
{
    /**
     * @return \Tuck\Sort\SortChain
     */
    public static function serverPrices(): SortChain
    {
        return Sort::chain()
            ->asc(self::byServerPriceGroups())
            ->asc(self::byServerMainPrices())
            ->asc(self::byHardwareType());
    }

    private static function byServerPriceGroups(): \Closure
    {
        return function (Price $price) {
            if (!$price->isOveruse() && $price->type !== 'monthly,hardware') {
                return 1;
            } elseif ($price->isOveruse()) {
                return 2;
            } elseif ($price->getSubtype() === 'hardware') {
                return 3;
            }

            return INF;
        };
    }

    private static function byServerMainPrices(): \Closure
    {
        $order = ['rack_unit', 'ip_num', 'support_time', 'backup_du', 'server_traf_max', 'server_traf95_max'];

        return function (Price $price) use ($order) {
            if (($key = array_search($price->getSubtype(), $order)) !== false) {
                return $key;
            }

            return INF;
        };
    }

    private static function byHardwareType(): \Closure
    {
        $order = ['CHASSIS', 'MOTHERBOARD', 'CPU', 'RAM', 'HDD', 'SSD'];

        return function (Price $price) use ($order) {
            $type = substr($price->object->name, 0, strpos($price->object->name, ':'));
            if (($key = array_search($type, $order)) !== false) {
                return $key;
            }

            return INF;
        };
    }
}
