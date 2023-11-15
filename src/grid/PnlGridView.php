<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
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
            'type' => [
                'attribute' => 'type',
            ],
            'month' => [
                'attribute' => 'month',
                'format' => ['date', 'php:Y-m-d'],
            ],
            'sum' => [
                'attribute' => 'sum',
                'value' => fn($model) => number_format((int)$model->sum / 100, 2),
            ],
            'charge_sum' => [
                'attribute' => 'charge_sum',
                'value' => fn($model) => number_format((int)$model->charge_sum / 100, 2),
            ],
            'discount_sum' => [
                'attribute' => 'discount_sum',
                'value' => fn($model) => number_format((int)$model->discount_sum / 100, 2),
            ],
        ]);
    }
}

