<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
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
                    list($date, $time) = explode(' ', $model->time, 2);

                    return $time === '00:00:00' ? Yii::$app->formatter->asDate($date) : Yii::$app->formatter->asDateTime($model->time);
                },
            ],
            'sum' => [
                'class'          => 'hipanel\grid\CurrencyColumn',
                'attribute'      => 'sum',
                'colors'         => ['danger' => 'warning'],
                'headerOptions'  => ['class' => 'text-right'],
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
            ],
            'balance' => [
                'class'          => 'hipanel\grid\CurrencyColumn',
                'headerOptions'  => ['class' => 'text-right'],
                'contentOptions' => function ($model, $key, $index) {
                    return ['class' => 'text-right' . ($index ? '' : ' text-bold')];
                },
            ],
            'gtype' => [
                'attribute' => 'gtype',
            ],
            'type_label' => [
                'class'           => 'hipanel\grid\RefColumn',
                'format'          => 'raw',
                'gtype'           => Yii::$app->user->can('support') ? 'type,bill' : 'type,bill,deposit',
                'filterAttribute' => 'gtype',
                'headerOptions'   => ['class' => 'text-right'],
                'filterOptions'   => ['class' => 'text-right'],
                'contentOptions'  => function ($model) {
                    return ['class' => 'text-right'];
                },
                'value'           => function ($model) {
                    static $colors = [
                        'correction' => 'normal',
                        'exchange'   => 'warning',
                        'deposit'    => 'success',
                    ];
                    $color = $colors[$model->gtype] ?: 'muted';
                    $qty   = $model->type === 'support_time' ? Yii::t('app', '{0, time, HH:mm}', ceil($model->quantity * 3600)) :
                           ($model->type === 'ip_num' ? $model->quantity : '');
                    $qty   = $qty ? ' - ' . Html::tag('b', $qty, ['class' => 'text-primary']) : '';

                    return Html::tag('b', $model->type_label, ['class' => "text-$color"]) . $qty;
                },
            ],
            'description' => [
                'attribute' => 'descr',
                'format'    => 'raw',
                'value'     => function ($model) {
                    $descr  = $model->descr ?: $model->label;
                    $text   = mb_strlen($descr) > 70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;
                    $tariff = $model->tariff ? Html::tag('span', Yii::t('app', 'Tariff') . ': ' . Html::a($model->tariff, ['@tariff/view', 'id' => $model->tariff_id]), ['class' => 'pull-right']) : '';
                    $object = $model->object ? implode(': ', array_filter([$model->class_label, static::objectLink($model)])) : '';

                    return $tariff . implode('<br>', array_filter([$object, $text]));
                },
            ],
            'tariff' => [
                'attribute' => 'tariff',
            ],
        ];
    }

    public static function objectLink($model)
    {
        return $model->class === 'device'
            ? Html::a($model->object, ['@server/view', 'id' => $model->object_id])
            : Html::tag('b', $model->object);
    }
}
