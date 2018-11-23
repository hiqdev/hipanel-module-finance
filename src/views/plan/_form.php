<?php

use hipanel\helpers\Url;
use hipanel\models\Ref;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\Plan;
use hipanel\widgets\RefCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var Plan $model
 */

?>

<?php $form = ActiveForm::begin([
    'id' => 'plan-form',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

<div class="row">
    <div class="col-md-4">
        <div class="box box-widget">
            <div class="box-body">
                <?php if (!$model->isNewRecord) : ?>
                    <?= Html::activeHiddenInput($model, 'id') ?>
                <?php endif ?>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'type')->dropDownList($model->typeOptions, ['prompt' => '--']) ?>
                <?= $form->field($model, 'client')->widget(ClientCombo::class) ?>
                <?= $form->field($model, 'currency')->widget(RefCombo::class, [
                    'gtype' => 'type,currency',
                    'i18nDictionary' => 'hipanel',
                    'findOptions' => [
                        'mapOptions' => ['to' => function (Ref $model) {
                            return strtoupper($model->name);
                        }]
                    ]
                ]) ?>
                <?= $form->field($model, 'is_grouping')->checkbox(); ?>
                <?= $form->field($model, 'note') ?>
            </div>
        </div>

        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
