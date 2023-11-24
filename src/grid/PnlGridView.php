<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\modules\client\grid\SellerColumn;
use yii\helpers\Html;

class PnlGridView extends BoxedGridView
{
    public function columns(): array
    {
        return array_merge(parent::columns(), [
            'charge_id' => [
                'format' => 'raw',
                'attribute' => 'charge_id',
                'value' => fn($model) => Html::a(
                    Html::encode($model->charge_id),
                    ['@bill/view', 'id' => $model->bill_id, '#' => $model->charge_id],
                    ['target' => '_blank']
                ),
            ],
            'seller' => [
                'class' => SellerColumn::class,
                'attribute' => 'seller_id',
                'label' => 'Customer',
            ],
            'type' => [
                'attribute' => 'type',
            ],
            'currency' => [
                'attribute' => 'currency',
                'enableSorting' => false,
            ],
            'month' => [
                'attribute' => 'month',
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
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
            'charge_date' => [
                'attribute' => 'charge_date',
                'enableSorting' => false,
                'filter' => false,
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
        ]);
    }
}

