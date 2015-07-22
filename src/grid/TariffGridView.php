<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (https://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;

class TariffGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'tariff' => [
                'class'           => MainColumn::className(),
                'filterAttribute' => 'tariff_like',
            ],
        ];
    }
}
