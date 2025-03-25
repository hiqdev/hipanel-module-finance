<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\forms\TargetManagementForm;
use hipanel\widgets\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var TargetManagementForm $model */

?>

<?php $form = ActiveForm::begin([
    'id' => 'close-sale-form',
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
    'options' => [
        'autocomplete' => 'off',
    ],
]) ?>

<?= Html::activeHiddenInput($model, 'customer_id') ?>
<?= Html::activeHiddenInput($model, 'target_id') ?>
<?= Html::activeHiddenInput($model, 'plan_id') ?>
<?= Html::activeHiddenInput($model, 'name') ?>

<?= $form->field($model, 'time')->widget(DateTimePicker::class, [
    'model' => $model,
    'options' => [
        'value' => Yii::$app->formatter->asDatetime(new DateTime(), 'php:Y-m-d H:i:s'),
    ],
]) ?>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel:finance', 'Close'), ['class' => 'btn btn-success']) ?> &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    </div>
</div>

<?php $form::end() ?>
