<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'id' => 'target-form',
    'enableClientValidation' => true,
    'validateOnBlur' => true,
]) ?>

<div class="row">
    <div class="col-lg-4 col-md-6">
        <div class="box box-widget sales">
            <div class="box-body">
                <div class="row">
                    <?php if (!$model->isNewRecord) : ?>
                        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                    <?php endif ?>
                    <div class="col-md-12">
                        <?= $form->field($model, 'name')->textInput() ?>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'client')->widget(ClientCombo::class) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'remoteid')->textInput() ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'type')->dropDownList($model->types, ['prompt' => '--']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'state')->dropDownList($model->states) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
&nbsp;
<?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>

<?php ActiveForm::end() ?>
