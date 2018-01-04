<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'plan-form',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

<div class="row">
    <div class="col-md-4">
        <div class="box box-widget">
            <div class="box-body">
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'type')->dropDownList($model->typeOptions, ['prompt' => '--']) ?>
                <?= $form->field($model, 'client')->widget(ClientCombo::class) ?>
                <?= $form->field($model, 'note') ?>
            </div>
        </div>

        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'),
            ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
