<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;
use hipanel\helpers\Url;
use yii\bootstrap\Html;

class PriceGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'plan' => [
                'class' => MainColumn::class,
                'note' => 'note',
                'value' => function ($model) {
                    return Html::a($model->plan, ['/finance/price/view', 'id' => $model->id], ['class' => 'bold']);
                },
                'noteOptions' => [
                    'url' => Url::to(['/finance/price/set-note']),
                ],
            ],
        ]);
    }
}
