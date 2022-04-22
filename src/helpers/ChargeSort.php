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

use hipanel\modules\finance\models\Charge;
use Tuck\Sort\Sort;
use Tuck\Sort\SortChain;

/**
 * Class PriceSort provides sorting functions for prices.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ChargeSort
{
    public static function anyCharges(): SortChain
    {
        return Sort::chain()
            ->asc(self::byType())
            ->asc(self::byHardwareType())
            ->compare(self::byName())
            ->asc(self::keepDiscountsWithParents());
    }

    private static function byHardwareType(): \Closure
    {
        $order = ['SERVER', 'CHASSIS', 'MOTHERBOARD', 'CPU', 'RAM', 'HDD', 'SSD'];

        return static function (Charge $charge) use ($order) {
            if ($charge->class === 'part' && !empty($charge->name)) {
                $type = substr($charge->name, 0, strpos($charge->name, ':'));
                if (($key = array_search($type, $order, true)) !== false) {
                    return $key;
                }
            }

            return INF;
        };
    }

    private static function byType(): \Closure
    {
        $order = [
            'rack',
            'rack_unit',
            'ip_num',
            'support_time',
            'private_cloud',
            'websa_private_cloud',
            'backup_du',
            'server_traf_max',
            'server_traf95_max',
            'server_du',
            'server_ssd',
            'server_sata',
            'win_license',
        ];

        return static function (Charge $charge) use ($order) {
            if (($key = array_search($charge->type, $order, true)) !== false) {
                return $key;
            }

            return INF;
        };
    }

    private static function keepDiscountsWithParents(): \Closure
    {
        return static function (Charge $charge) {
            return $charge->parent_id !== null
                ? $charge->parent_id * 10 + 2
                : $charge->id * 10 + 1;
        };
    }

    private static function byName(): \Closure
    {
        return static fn(Charge $a, Charge $b) => strnatcasecmp($a->name ?? '', $b->name ?? '');
    }
}
