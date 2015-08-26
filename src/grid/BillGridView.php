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

use hipanel\grid\CurrencyColumn;
use hipanel\grid\MainColumn;
use hipanel\widgets\ArraySpoiler;

class BillGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'bill' => [
                'class'           => MainColumn::className(),
                'attribute'       => 'bill',
                'filterAttribute' => 'bill_like',
            ],
            'time' => [
                'format' => 'date',
                'filter' => false,
            ],
            'sum' => [
                'class'         => CurrencyColumn::className(),
                'attribute'     => 'sum',
                'nameAttribute' => 'sum',
            ],
            'balance' => [
                'class' => CurrencyColumn::className(),
            ],
            'gtype' => [
                'attribute' => 'gtype',
            ],
/* XXX didn't find Description column or widget
            'descriptionOld' => [
                'class'                 => Description::className(),
                'attribute'             => 'descr',
                'filter'                => false,
                'fields'                => [
                    'domain'                => "{object} {type} {quantity} domains {descr}",
                    'feature'               => "{type_label} {label} {object}: {descr}",
                    'premium_package'       => "{type_label} {label} {object}",
                    'intercept'             => "{object} {type_label} {descr}",
                    'deposit'               => "{type_label} {label} {object}: {descr|txn}",
                    'default'               => "{type_label} {label} {object}: {descr|tariff}",
                ],
            ],
*/
            'description' => [
                'attribute' => 'descr',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return strpos($model->descr, ',')===false ? $model->descr : ArraySpoiler::widget(['data' => $model->descr]);
                },
            ],
        ];
    }
}
