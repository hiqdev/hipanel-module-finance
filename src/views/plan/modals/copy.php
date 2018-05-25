<?php

use hipanel\modules\server\widgets\combo\ServerCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
$model->id = 1502584;

?>
<?php $form = ActiveForm::begin([
    'options' => [
        'id' => $model->scenario . '-form',
    ],
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute(['validate-single-form', 'scenario' => $model->scenario]),
]) ?>

<?= Html::activeHiddenInput($model, "id") ?>

<?= $form->field($model, 'server_ids')->widget(ServerCombo::class, ['inputOptions' => ['multiple' => true], 'hasId' => true]) ?>

<?= Html::submitButton(Yii::t('hipanel', 'Create'), ['class' => 'btn btn-success']) ?> &nbsp;
<?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>

<?php $form::end() ?>

