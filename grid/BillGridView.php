<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;

class BillGridView extends \hipanel\grid\BoxedGridView
{
    static public function defaultColumns()
    {
        return [
            'bill' => [
                'class'                 => MainColumn::className(),
                'filterAttribute'       => 'bill_like',
            ],
        ];
    }
}
