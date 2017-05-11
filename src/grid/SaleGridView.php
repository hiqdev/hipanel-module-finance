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
                'class' => 'hipanel\grid\MainColumn',
                'filterAttribute' => 'tariff_like',
                'value' => function ($model) {
                    return Html::a($model->tariff, ['@tariff/view', 'id' => $model->tariff_id]);
                }
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
            ],
            'buyer' => [
                'class' => ClientColumn::class,
                'idAttribute' => 'buyer_id',
                'attribute' => 'buyer_id',
                'nameAttribute' => 'buyer',
            ],
            'object' => [
                'format' => 'html',
                'value' => function ($model) {
                    return LinkToObjectResolver::widget(['model' => $model]);
                }
            ],
        ];
    }
}
