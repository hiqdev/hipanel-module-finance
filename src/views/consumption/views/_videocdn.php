<?php

use hipanel\modules\finance\grid\TargetGridView;
use hipanel\modules\finance\models\Target;
use yii\helpers\Html;

/** @var Target $mainObject */

?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::a(Html::encode($this->title), ['@target/view', 'id' => $mainObject->name]) ?></h3>
    </div>
    <div class="box-body no-padding">
        <?= TargetGridView::detailView([
            'boxed' => false,
            'model' => $mainObject,
            'columns' => [
                'name',
                'tariff',
                'client',
            ],
        ]) ?>
    </div>
</div>
