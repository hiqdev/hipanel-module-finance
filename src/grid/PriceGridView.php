<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\RefColumn;
use hipanel\modules\finance\menus\PriceActionsMenu;
use hiqdev\yii2\menus\grid\MenuColumn;
use yii\bootstrap\Html;

class PriceGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'plan' => [
                'format' => 'html',
                'filterAttribute' => 'plan_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'value' => function ($model) {
                    return Html::a($model->plan, ['@plan/view', 'id' => $model->plan_id]);
                },
            ],
            'type' => [
                'class' => RefColumn::class,
                'attribute' => 'type',
                'filterAttribute' => 'type',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'type,bill',
                'findOptions' => [
                    'select' => 'oname_label',
                    'pnames' => 'monthly,overuse',
                    'with_recursive' => 1,
                    'mapOptions' => ['from' => 'oname'],
                ],
            ],
            'unit' => [
                'class' => RefColumn::class,
                'attribute' => 'unit',
                'filterAttribute' => 'unit',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'type,unit',
                'findOptions' => [
                    'with_recursive' => 1,
                    'select' => 'oname_label',
                    'mapOptions' => ['from' => 'oname'],
                ],
            ],
            'currency' => [
                'class' => RefColumn::class,
                'attribute' => 'currency',
                'filterAttribute' => 'currency',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'type,currency',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => PriceActionsMenu::class,
            ],
        ]);
    }
}
