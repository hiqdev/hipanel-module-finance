<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\forms\TargetManagementForm;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hipanel\widgets\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var TargetManagementForm $model */

?>

<?php $form = ActiveForm::begin([
    'id' => 'sale-form',
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
    'options' => [
        'autocomplete' => 'off',
    ],
]) ?>

<?= Html::activeHiddenInput($model, 'customer_id') ?>
<?= Html::activeHiddenInput($model, 'remoteid') ?>
<?= Html::activeHiddenInput($model, 'type') ?>
<?= Html::activeHiddenInput($model, 'name') ?>

<?= $form->field($model, 'plan_id')->widget(PlanCombo::class, ['hasId' => true, 'tariffType' => $model->type]) ?>
<?= $form->field($model, 'time')->widget(DateTimePicker::class, [
    'model' => $model,
    'clientOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd hh:ii:ss',
    ],
    'options' => [
        'value' => Yii::$app->formatter->asDatetime(new DateTime(), 'php:Y-m-d H:i:s'),
    ],
]) ?>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel:finance', 'Change'), ['class' => 'btn btn-success']) ?> &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    </div>
</div>

<?php $form::end() ?>
