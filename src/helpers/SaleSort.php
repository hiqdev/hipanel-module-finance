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

use hipanel\modules\finance\models\FakeGroupingSale;
use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\FakeSharedSale;
use hipanel\modules\finance\models\Sale;
use Tuck\Sort\Sort;
use Tuck\Sort\SortChain;

/**
 * Class SaleSort.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class SaleSort
{
    public static function toDisplayInPlan(): SortChain
    {
        return Sort::chain()
            ->asc(self::bySaleClass())
            ->compare(self::byTargetObjectName());
    }

    private static function bySaleClass(): \Closure
    {
        $map = [
            FakeSharedSale::class => 1,
            FakeGroupingSale::class => 2,
            FakeSale::class => 4,
            Sale::class => 3,
        ];

        return function (Sale $sale) use ($map) {
            foreach ($map as $class => $order) {
                if ($sale instanceof $class) {
                    return $order;
                }
            }

            return INF;
        };
    }

    private static function byTargetObjectName(): \Closure
    {
        return function (Sale $a, Sale $b) {
            return strnatcasecmp($a->object, $b->object);
        };
    }
}
