<?php

use hipanel\assets\BootstrapDatetimepickerAsset;
use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\forms\TargetManagementForm;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var TargetManagementForm $model */

BootstrapDatetimepickerAsset::register($this);
$locale = Yii::$app->language;
$this->registerJs(/* @lang JavaScript */ <<<"JS"
    const dateInput = $('input[name*=time]');
    dateInput.datetimepicker({
      date: moment(),
      locale: '$locale',
      viewMode: 'months',
      format: 'YYYY-MM-01'
    });
JS
);
?>

<?php $form = ActiveForm::begin([
    'id' => 'change-plan-form',
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
    'options' => [
        'autocomplete' => 'off',
    ],
]) ?>

<?= Html::activeHiddenInput($model, 'remoteid') ?>
<?= Html::activeHiddenInput($model, 'type') ?>
<?= Html::activeHiddenInput($model, 'name') ?>

<?= $form->field($model, 'plan_id')->widget(PlanCombo::class, ['hasId' => true, 'tariffType' => $model->type]) ?>
<?= $form->field($model, 'customer_id')->widget(ClientCombo::class) ?>
<?= $form->field($model, 'time') ?>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel:finance', 'Change'), ['class' => 'btn btn-success']) ?> &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    </div>
</div>

<?php $form::end() ?>
