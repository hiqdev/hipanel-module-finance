<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Plan;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

class PlanAttributes extends Widget
{
    public Plan $plan;

    public function run()
    {
        return GridView::widget([
            'layout' => '{items}',
            'tableOptions' => ['class' => 'table table-striped', 'style' => 'margin: 0'],
            'showHeader' => false,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $this->plan->getPlanAttributes(),
                'sort' => false,
                'pagination' => false,
            ]),
            'columns' => ['name', 'value'],
        ]);
    }
}
