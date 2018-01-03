<?php

namespace hipanel\modules\finance\grid;

use yii\bootstrap\Html;

class PriceGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'plan' => [
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a($model->plan, ['/finance/plan/view', 'id' => $model->plan_id]);
                },
            ],
        ]);
    }
}
