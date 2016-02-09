<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hiqdev\combo\StaticCombo;
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;

?>

<div class="col-md-4">
    <div class="form-group md-mb-5">
        <?= Html::label(Yii::t('app', 'Date')) ?>
        <?= DatePicker::widget([
            'model' => $search->model,
            'attribute' => 'time_from',
            'attribute2' => 'time_till',
            'type' => DatePicker::TYPE_RANGE,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd.mm.yyyy',
            ],
        ]) ?>
    </div>
    <?= $search->field('type')->widget(StaticCombo::classname(), [
        'data' => $type,
        'hasId' => true,
        'pluginOptions' => [
            'select2Options' => [
                'multiple' => true,
            ],
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('support')) : ?>
        <?= $search->field('client_id')->widget(ClientCombo::classname()) ?>
    <?php endif ?>
</div>

<div class="col-md-4">
    <?= FieldRange::widget([
        'model' => $search->model,
        'attribute1' => 'sum_gt',
        'attribute2' => 'sum_lt',
        'label' => Yii::t('app', 'Sum'),
    ]) ?>

    <?= $search->field('descr') ?>

    <?php if (Yii::$app->user->can('support')) : ?>
        <?= $search->field('seller_id')->widget(SellerCombo::classname()) ?>
    <?php endif ?>
</div>

<div class="col-md-4">
    <?= $search->field('currency')->widget(StaticCombo::classname(), [
        'data' => ['usd' => 'USD', 'eur' => 'EUR'],
        'hasId' => true,
        'pluginOptions' => [
            'select2Options' => [
                'multiple' => false,
            ],
        ],
    ]) ?>
</div>
