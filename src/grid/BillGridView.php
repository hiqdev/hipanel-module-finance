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

use hipanel\widgets\ArraySpoiler;
use Yii;
use yii\helpers\Html;

class BillGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'bill' => [
                'class'           => 'hipanel\grid\MainColumn',
                'attribute'       => 'bill',
                'filterAttribute' => 'bill_like',
            ],
            'time' => [
                'format'         => 'html',
                'filter'         => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value'          => function ($model) {
                    list($date, $time) = explode(' ',$model->time,2);
                    return $time=='00:00:00' ? Yii::$app->formatter->asDate($date) : Yii::$app->formatter->asDateTime($model->time);
                },
            ],
            'sum' => [
                'class'          => 'hipanel\grid\CurrencyColumn',
                'attribute'      => 'sum',
                'colors'         => ['danger' => 'warning'],
                'nameAttribute'  => 'sum',
                'headerOptions'  => ['class' => 'text-right'],
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right' . ($model->sum>0 ? ' text-bold' : '')];
                }
            ],
            'balance' => [
                'class'          => 'hipanel\grid\CurrencyColumn',
                'headerOptions'  => ['class' => 'text-right'],
                'contentOptions' => function ($model, $key, $index) {
                    return ['class' => 'text-right' . ($index ? '' : ' text-bold')];
                }
            ],
            'gtype' => [
                'attribute' => 'gtype',
            ],
            'type_label' => [
                'attribute' => 'type_label',
                'headerOptions'  => ['class' => 'text-right'],
                'filterOptions'  => ['class' => 'text-right'],
                'contentOptions' => function ($model) {
                    static $colors = [
                        'correction' => 'normal',
                        'exchange'   => 'warning',
                        'deposit'    => 'success',
                    ];
                    $color = $colors[$model->gtype] ?: 'muted';
                    return ['class' => "text-right text-bold text-$color"];
                },
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
                    $qty    = $model->type=='support_time' ? Yii::t('app', '{0, time, HH:mm}', ceil($model->quantity * 3600)) :
                            ( $model->type=='ip_num' ? $model->quantity : '');
                    $qty    = $qty ? Html::tag('b', $qty . ' - ', ['class' => 'text-primary']) : '';
                    $descr  = $model->descr ?: $model->label;
                    $text   = mb_strlen($descr)>70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;
                    $tariff = $model->tariff ? Html::tag('b', Yii::t('app', 'Tariff')) . ': ' . $model->tariff : '';
                    return $qty . $text . ($text && $tariff ? '<br>' : '') . $tariff;
                },
            ],
            'tariff' => [
                'attribute' => 'tariff',
            ],
        ];
    }
}
