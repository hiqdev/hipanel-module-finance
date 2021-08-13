<?php

use hipanel\modules\server\grid\ServerGridView;
use hipanel\modules\server\models\Server;
use yii\helpers\Html;

/** @var Server $mainObject */

?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::a(Html::encode($this->title), ['@target/view', 'id' => $mainObject->name]) ?></h3>
    </div>
    <div class="box-body no-padding">
        <?= ServerGridView::detailView([
            'boxed' => false,
            'model' => $mainObject,
            'columns' => [
                'client_id', 'seller_id',
                [
                    'attribute' => 'name',
                    'contentOptions' => ['class' => 'text-bold'],
                ], 'detailed_type',
                'ip', 'note', 'label',
                'mails_num',
            ],
        ]) ?>
    </div>
</div>
