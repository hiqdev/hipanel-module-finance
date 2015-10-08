<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hiqdev\combo\StaticCombo;
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;

?>

<div class="col-md-4">
    <?= $search->field('type')->widget(StaticCombo::classname(), [
        'data' => $type,
        'hasId' => true,
        'pluginOptions' => [
            'select2Options' => [
                'multiple' => false,
            ]
        ],
    ]) ?>

    <?= $search->field('time_from')->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_RANGE,
        'attribute2' => 'time_till',
        'separator' => Yii::t('app', '&larr; between &rarr;'),
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd.mm.yyyy',
        ],
    ])->label(Yii::t('app', 'Date range')) ?>

    <?php if (Yii::$app->user->can('support')) { ?>
        <?= $search->field('client_id')->widget(ClientCombo::classname()) ?>
    <?php } ?>
</div>

<div class="col-md-4">
    <?= $search->field('descr') ?>

    <?= FieldRange::widget([
        'model' => $search->model,
        'attribute1' => 'sum_gt',
        'attribute2' => 'sum_lt',
        'label' => Yii::t('app', 'Sum'),
        'separator' => Yii::t('app', '&larr; between &rarr;'),
    ]) ?>

    <?php if (Yii::$app->user->can('support')) { ?>
        <?= $search->field('seller_id')->widget(SellerCombo::classname()) ?>
    <?php } ?>
</div>
