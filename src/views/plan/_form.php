<?php

use hipanel\helpers\Url;
use hipanel\models\Ref;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\models\Plan;
use hipanel\widgets\CustomAttributesForm;
use hipanel\widgets\RefCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Plan $model
 */

$this->registerCss('
  #plan-form .container-items .form-group { position: relative; }
  #plan-form .container-items .help-block-error { position: absolute; top: 46px; }
');

?>

<?php $form = ActiveForm::begin([
    'id' => 'plan-form',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

<div class="row">
    <div class="col-md-4">
        <div class="box box-widget">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <?= Yii::t('hipanel:finance', 'Tariff plan') ?>
                </h4>
            </div>
            <div class="box-body">
                <?php if (!$model->isNewRecord) : ?>
                    <?= Html::activeHiddenInput($model, 'id') ?>
                <?php endif ?>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'type')->dropDownList($model->typeOptions, ['prompt' => '--']) ?>
                <?= $form->field($model, 'client')->widget(SellerCombo::class) ?>
                <?= $form->field($model, 'currency')->widget(RefCombo::class, [
                    'gtype' => 'type,currency',
                    'i18nDictionary' => 'hipanel',
                    'findOptions' => [
                        'mapOptions' => ['to' => static fn(Ref $model) => strtoupper($model->name)],
                    ],
                ]) ?>
                <?= $form->field($model, 'is_grouping')->checkbox() ?>
                <?= $form->field($model, 'note') ?>
            </div>
        </div>
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
    <div class="col-md-6">
        <div class="box box-widget">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <?= Yii::t('hipanel:finance', 'Attributes') ?>
                </h4>
            </div>
            <div class="box-body no-padding">
                <?= CustomAttributesForm::widget(['form' => $form, 'owner' => $model]) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
