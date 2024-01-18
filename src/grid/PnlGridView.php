<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\models\Pnl;
use Yii;
use yii\helpers\Html;

class PnlGridView extends BoxedGridView
{
    public function columns(): array
    {
        return array_merge(parent::columns(), [
            'charge_id' => [
                'format' => 'raw',
                'attribute' => 'charge_id',
                'enableSorting' => false,
                'label' => Yii::t('hipanel:finance', 'Charge'),
                'value' => function (Pnl $model): string {
                    $id = Html::a(
                        Html::encode($model->charge_id),
                        [
                            '@bill/view',
                            'id' => $model->bill_id,
                            '#' => $model->charge_id,
                        ],
                        ['target' => '_blank', 'alt' => $model->charge_id]
                    );
                    $type = Html::tag('span', $model->charge_type, ['class' => 'text-bold', 'style' => ['font-size' => 'smaller']]);
                    $label = Html::encode($model->charge_label);
                    $object = strtoupper($model->commonObject['type']) . ': ' . $model->commonObject['name'];
                    $label = Html::tag(
                        'span',
                        nl2br(implode("\n", array_filter([$label, $object]))),
                        ['style' => ['color' => '#999', 'display' => 'inline-block']]
                    );

                    return Html::tag('span',
                            implode("\n", [
                                $id,
                                $type,
                            ]),
                            [
                                'style' => [
                                    'display' => 'flex',
                                    'flex-direction' => 'column',
                                    'margin-bottom' => '.5em',
                                ],
                            ]) . $label;
                },
            ],
            'note' => [
                'attribute' => 'note',
                'filterOptions' => ['class' => 'narrow-filter'],
                'enableSorting' => false,
            ],
            'client' => [
                'attribute' => 'client',
                'label' => 'Customer',
                'enableSorting' => false,
                'format' => 'raw',
                'filterAttribute' => 'client',
                'value' => fn($model): string => Html::a($model->client, ['@client/view', 'id' => $model->client_id]),
            ],
            'type' => [
                'attribute' => 'type',
            ],
            'currency' => [
                'attribute' => 'currency',
                'filterOptions' => ['class' => 'narrow-filter'],
                'enableSorting' => false,
            ],
            'month' => [
                'attribute' => 'month',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => ['date', 'php:Y-m-d'],
            ],
            'sum' => [
                'attribute' => 'sum',
                'value' => fn($model) => number_format((int)$model->sum / 100, 2),
                'enableSorting' => false,
                'filter' => false,
            ],
            'charge_sum' => [
                'attribute' => 'charge_sum',
                'value' => fn($model) => number_format((int)$model->charge_sum / 100, 2),
                'enableSorting' => false,
                'filter' => false,
            ],
            'discount_sum' => [
                'attribute' => 'discount_sum',
                'value' => fn($model) => number_format((int)$model->discount_sum / 100, 2),
                'enableSorting' => false,
                'filter' => false,
            ],
            'rate' => [
                'attribute' => 'rate',
                'enableSorting' => false,
                'filter' => false,
            ],
            'eur_amount' => [
                'attribute' => 'eur_amount',
                'enableSorting' => false,
                'filter' => false,
                'value' => fn($pnl): string => $pnl->eur_amount ? $this->formatter->asCurrency($pnl->eur_amount / 100, 'eur') : '',
            ],
            'exchange_date' => [
                'attribute' => 'exchange_date',
                'enableSorting' => false,
                'filter' => false,
                'format' => ['date', 'php:Y-m-d'],
            ],
            'charge_date' => [
                'attribute' => 'charge_date',
                'enableSorting' => false,
                'filter' => false,
                'format' => ['date', 'php:Y-m-d'],
            ],
        ]);
    }
}

