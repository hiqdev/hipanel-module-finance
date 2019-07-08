<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\ContactCombo;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('hipanel:finance', 'Reserve number');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Requisites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
    'id' => 'contact-form',
    'action' => $action ?: $model->scenario,
    'enableClientValidation' => true,
    'validateOnBlur' => true,
    'enableAjaxValidation' => true,
    'layout' => 'horizontal',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'client_id')->widget(ClientCombo::class) ?>
    <?= $form->field($model, 'recipient_id')->widget(ContactCombo::class) ?>
    <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>

<?php $form->end() ?>


