<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

class TariffGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'tariff' => [
                'class'           => 'hipanel\grid\MainColumn',
                'filterAttribute' => 'tariff_like',
            ],
            'used' => [
                'filter' => false,
            ],
            'note' => [
                'class' => 'hiqdev\xeditable\grid\XEditableColumn',
            ],
        ];
    }
}
