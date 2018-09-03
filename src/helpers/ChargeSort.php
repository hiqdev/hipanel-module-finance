<?php

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
            ->asc(self::keepDiscountsWithParents());
    }

    private static function byType(): \Closure
    {
        $order = ['rack_unit', 'ip_num', 'support_time', 'backup_du', 'server_traf_max', 'server_traf95_max'];

        return function (Charge $charge) use ($order) {
            if (($key = array_search($charge->type, $order, true)) !== false) {
                return $key;
            }

            return INF;
        };
    }

    private static function keepDiscountsWithParents(): \Closure
    {
        return function (Charge $charge) {
            $a = $charge->parent_id !== null
                ? $charge->parent_id * 10 + 2
                : $charge->id * 10 + 1;

            return $a;
        };
    }
}
