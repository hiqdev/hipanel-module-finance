<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\CurrencyColumn;
use hipanel\grid\MainColumn;
use hipanel\helpers\Url;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hipanel\widgets\ArraySpoiler;
use Yii;
use yii\helpers\Html;

class BillGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'bill' => [
                'class' => MainColumn::class,
                'attribute' => 'bill',
                'filterAttribute' => 'bill_like',
            ],
            'time' => [
                'format' => 'html',
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    list($date, $time) = explode(' ', $model->time, 2);

                    return $time === '00:00:00' ? Yii::$app->formatter->asDate($date) : Yii::$app->formatter->asDateTime($model->time);
                },
            ],
            'sum' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
            ],
            'sum_editable' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'urlCallback' => function ($model, $key) {
                    return Url::to(['bill/update', 'id' => $model->id]);
                },
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
            ],
            'balance' => [
                'class' => CurrencyColumn::class,
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model, $key, $index) {
                    return ['class' => 'text-right' . ($index ? '' : ' text-bold')];
                },
            ],
            'gtype' => [
                'attribute' => 'gtype',
            ],
            'type_label' => [
                'class' => \hipanel\grid\DataColumn::class,
                'filter' => function ($column, $filterModel) {
                    return BillTypeFilter::widget([
                        'options' => ['class' => 'form-control text-right'],
                        'attribute' => 'ftype',
                        'model' => $filterModel,
                    ]);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right'];
                },
                'value' => function ($model) {
                    static $colors = [
                        'correction' => 'normal',
                        'exchange' => 'warning',
                        'deposit' => 'success',
                    ];
                    $color = $colors[$model->gtype] ?: 'muted';

                    return Html::tag('b', Yii::t('hipanel:finance', $model->type_label), ['class' => "text-$color"]);
                },
            ],
            'description' => [
                'attribute' => 'descr',
                'format' => 'raw',
                'value' => function ($model) {
                    $descr = $model->descr ?: $model->label;
                    $text = mb_strlen($descr) > 70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;
                    $tariff = $model->tariff ? Html::tag('span',
                        Yii::t('hipanel', 'Tariff') . ': ' . Html::a($model->tariff,
                            ['@tariff/view', 'id' => $model->tariff_id]), ['class' => 'pull-right']) : '';
                    $amount = static::billQuantity($model);
                    $object = $model->object ? implode(':&nbsp;',
                        [$model->class_label, static::objectLink($model)]) : '';

                    return $tariff . $amount . ' ' . implode('<br>', array_filter([$object, $text]));
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

    /**
     * @param $model
     * @return null|string
     */
    public static function billQuantity($model)
    {
        switch ($model->type) {
            case 'support_time':
                $text = Yii::t('hipanel:finance', '{quantity, time, HH:mm} hour(s)',
                    ['quantity' => ceil($model->quantity * 3600)]);
                break;
            case 'server_traf_max':
            case 'backup_du':
                $text = Yii::$app->formatter->asShortSize($model->quantity * 1024 * 1024 * 1024);
                break;
            case 'ip_num':
                $text = Yii::t('hipanel:finance', '{quantity} IP', ['quantity' => $model->quantity]);
                break;
            default:
                return null;
        }

        return Html::tag('nobr', Html::tag('b', $text));
    }
}
