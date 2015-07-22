<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;
use hipanel\grid\CurrencyColumn;
use hipanel\widgets\ArraySpoiler;
use Yii;
use yii\helpers\Html;

class BillGridView extends \hipanel\grid\BoxedGridView
{
    static public function defaultColumns()
    {
        return [
            'bill' => [
                'class'                 => MainColumn::className(),
                'attribute'             => 'bill',
                'filterAttribute'       => 'bill_like',
            ],
            'time' => [
                'format'                => 'date',
            ],
            'sum' => [
                'class'                 => CurrencyColumn::className(),
                'attribute'             => 'sum',
                'nameAttribute'         => 'sum'
            ],
            'balance' => [
                'class'                 => CurrencyColumn::className(),
            ],
            'gtype' => [
                'attribute'             => 'gtype',
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
                'attribute'             => 'descr',
                'format'                => 'raw',
                'value'                 => function ($model) {
                    return ArraySpoiler::widget(['data' => $model->descr]);
                },
            ],
        ];
    }
}
