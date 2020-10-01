<?php

use hipanel\helpers\Url;
use hipanel\models\Ref;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\PlanAttribute;
use hipanel\widgets\DynamicFormWidget;
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
$this->registerJs(/** @lang ECMAScript 6 */ '
$(".remove-attribute").click(() => {
  if ($(".attribute-item").length === 1) {
    $(".container-items :input").val("");
  }
});
');

$customAttributes = empty($model->getPlanAttributes()) ? [new PlanAttribute()] : $model->getPlanAttributes();
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
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper',
                    'widgetBody' => '.container-items',
                    'widgetItem' => '.attribute-item',
                    'insertButton' => '.add-attribute',
                    'deleteButton' => '.remove-attribute',
                    'model' => $customAttributes[0],
                    'formId' => 'plan-form',
                    'formFields' => [
                        'name',
                        'value',
                    ],
                ]) ?>
                <table class="table table-striped table-condensed">
                    <thead>
                    <tr>
                        <th><?= Yii::t('hipanel:finance', 'Name') ?></th>
                        <th><?= Yii::t('hipanel:finance', 'Value') ?></th>
                        <th class="text-center" style="width: 90px;">
                            <button type="button" class="add-attribute btn bg-olive btn-sm"
                                    title="<?= Yii::t('hipanel', 'Add new') ?>">
                                <?= Html::tag('span', null, ['class' => 'fa fa-fw fa-plus']) ?>
                            </button>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="container-items">
                    <?php foreach ($customAttributes as $idx => $attribute): ?>
                        <tr class="attribute-item">
                            <td class="text-center" style="vertical-align: middle">
                                <?= $form->field($attribute, "[{$idx}]name")->label('')->textInput(['maxlength' => true]) ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle">
                                <?= $form->field($attribute, "[{$idx}]value")->label('')->textInput(['maxlength' => true]) ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle">
                                <button type="button" class="remove-attribute btn btn-danger btn-sm"
                                        title="<?= Yii::t('hipanel', 'Remove') ?>">
                                    <?= Html::tag('span', null, ['class' => 'fa fa-fw fa-minus']) ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
