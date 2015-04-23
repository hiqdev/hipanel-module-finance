<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;
use hipanel\grid\CurrencyColumn;

class BillGridView extends \hipanel\grid\BoxedGridView
{
    static public function defaultColumns()
    {
        return [
            'bill'      => [
                'class'                 => MainColumn::className(),
                'attribute'             => 'bill',
                'filterAttribute'       => 'bill_like',
            ],
            'time'      => [
                'format'                => 'date',
            ],
            'sum'       => [
                'class'                 => CurrencyColumn::className(),
                'attribute'             => 'sum',
                'nameAttribute'         => 'sum'
            ],
            'balance'   => [
                'class'                 => CurrencyColumn::className(),
            ],
        ];
    }
}
