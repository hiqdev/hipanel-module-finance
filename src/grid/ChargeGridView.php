<?php

namespace hipanel\modules\finance\grid;


use hipanel\grid\CurrencyColumn;
use hipanel\widgets\ArraySpoiler;
use Yii;
use yii\helpers\Html;

class ChargeGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'type_label' => [
                'label' => Yii::t('hipanel', 'Type'),
                'format' => 'html',
                'value' => function ($model) {
                    static $colors = [
                        'correction' => 'normal',
                        'exchange' => 'warning',
                        'deposit' => 'success',
                    ];
                    $color = $colors[$model->type] ?: 'muted';

                    return Html::tag('b', Yii::t('hipanel:finance', $model->type_label), ['class' => "text-$color"]);
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
            'label' => [
                'attribute' => 'label',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->name) {
                        $descr = Yii::t('hipanel', ucfirst($model->class)) . ' ' . Html::tag('b', $model->name);
                    } elseif ($model->label) {
                        $descr = $model->label;
                    }
                    $text = mb_strlen($descr) > 70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;

                    return $text;
                },
            ],
            'quantity' => [
                'attribute' => 'quantity',
                'format' => 'html',
                'value' => function ($model) {
                    return BillGridView::billQuantity($model);
                }
            ],
            'time' => [
                'format' => 'html',
                'filter' => false,
                'enableSorting' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    list($date, $time) = explode(' ', $model->time, 2);

                    if (in_array($model->type, [
                        'discount', 'domain', 'monthly', 'overuse', 'premium_package',
                        'feature', 'intercept', 'periodic',
                    ], true)) {
                        return Yii::$app->formatter->asDate($date, 'LLLL y');
                    }

                    return $time === '00:00:00' ? Yii::$app->formatter->asDate($date) : Yii::$app->formatter->asDateTime($model->time);
                },
            ],
        ]);
    }
}
