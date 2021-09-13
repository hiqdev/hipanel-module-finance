<?php

use hipanel\modules\client\grid\ClientGridView;
use hipanel\modules\client\models\Client;
use yii\helpers\Html;

/** @var Client $mainObject */

?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::a(Html::encode($this->title), ['@client/view', 'id' => $mainObject->id]) ?></h3>
    </div>
    <div class="box-body no-padding">
        <?= ClientGridView::detailView([
            'boxed' => false,
            'model' => $mainObject,
            'columns' => array_filter([
                'seller_id', 'referer_id', 'name',
                Yii::$app->user->not($mainObject->id) ? 'note' : null,
                Yii::$app->user->not($mainObject->id) ? 'description' : null,
                'type', 'state',
            ]),
        ]) ?>
    </div>
</div>
