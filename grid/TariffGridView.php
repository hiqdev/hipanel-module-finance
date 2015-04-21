<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;

class TariffGridView extends \hipanel\grid\BoxedGridView
{
    static public function defaultColumns()
    {
        return [
            'tariff' => [
                'class'                 => MainColumn::className(),
                'filterAttribute'       => 'tariff_like',
            ],
        ];
    }
}
