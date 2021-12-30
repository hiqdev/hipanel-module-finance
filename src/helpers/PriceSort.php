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

use Closure;
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
    public static function anyPrices(): SortChain
    {
        return Sort::chain()
            ->compare(self::serverPrices())
            ->compare(self::byTargetObjectName());
    }

    /**
     * @return SortChain
     */
    public static function serverPrices(): SortChain
    {
        return Sort::chain()
            ->asc(self::byServerPriceGroups())
            ->asc(self::byObjectType())
            ->asc(self::byServerMainPrices())
            ->asc(self::byHardwareType())
            ->compare(self::byTargetObjectName())
            ->compare(self::byServerPriceType());
    }

    public static function zonePrices(): SortChain
    {
        return Sort::chain()->asc(self::byObjectNo());
    }

    private static function byServerPriceType()
    {
        return static function (Price $a, Price $b) {
            if ($a->getSubtype() === $b->getSubtype()) {
                return $a->isOveruse() ? 1 : -1;
            }

            return 0;
        };
    }

    private static function byServerPriceGroups(): Closure
    {
        return function (Price $price) {
            if ($price->type !== 'monthly,hardware') {
                return 1;
            }

            if ($price->getSubtype() === 'hardware') {
                return 2;
            }

            return INF;
        };
    }

    private static function byServerMainPrices(): Closure
    {
        $order = [
            'rack',
            'rack_unit',
            'ip_num',
            'support_time',
            'backup_du',
            'server_traf_max',
            'cdn_traf_max',
            'server_traf95_max',
            'cdn_traf95_max',
            'server_du',
            'storage_du95',
            'cdn_cache95',
            'server_ssd',
            'server_sata',
            'win_license',
        ];

        return static function (Price $price) use ($order) {
            if (($key = array_search($price->getSubtype(), $order, true)) !== false) {
                return $key;
            }

            return INF;
        };
    }

    private static function byHardwareType(): Closure
    {
        $order = ['SERVER', 'CHASSIS', 'MOTHERBOARD', 'CPU', 'RAM', 'HDD', 'SSD'];

        return function (Price $price) use ($order) {
            $type = substr($price->object->name, 0, strpos($price->object->name, ':'));
            if (($key = array_search($type, $order, true)) !== false) {
                return $key;
            }

            return INF;
        };
    }

    private static function byTargetObjectName(): Closure
    {
        return function (Price $a, Price $b) {
            return strnatcasecmp($a->object->name, $b->object->name);
        };
    }

    private static function byObjectType(): Closure
    {
        $order = ['dedicated', 'net', 'model_group', 'part'];

        return function (Price $price) use ($order) {
            if (($key = array_search($price->object->type, $order, true)) !== false) {
                return $key;
            }

            return INF;
        };
    }

    private static function byObjectNo(): Closure
    {
        return static function ($group): int {
            return reset($group)->object->no;
        };
    }
}
