<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use yii\helpers\Html;

class SaleGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'tariff' => [
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a($model->tariff, ['@tariff/view', 'id' => $model->tariff_id]);
                },
                'enableSorting' => false,
            ],
            'time' => [
                'format' => ['datetime'],
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
            'seller' => [
                'class' => ClientColumn::class,
                'idAttribute' => 'seller_id',
                'attribute' => 'seller_id',
                'nameAttribute' => 'seller',
                'enableSorting' => false,
            ],
            'buyer' => [
                'class' => ClientColumn::class,
                'idAttribute' => 'buyer_id',
                'attribute' => 'buyer_id',
                'nameAttribute' => 'buyer',
                'enableSorting' => false,
            ],
            'object' => [
                'format' => 'html',
                'filterAttribute' => 'object_like',
                'enableSorting' => false,
                'value' => function ($model) {
                    return LinkToObjectResolver::widget(['model' => $model]);
                }
            ],
        ];
    }
}
