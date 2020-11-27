<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\widgets\combo\RequisitesCombo;
use hipanel\widgets\AmountWithCurrency;
use hipanel\widgets\DateTimePicker;
use hipanel\widgets\DynamicFormWidget;
use hipanel\widgets\combo\ObjectCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var hipanel\modules\finance\forms\BillForm[] $models */
/** @var array $billTypes */
/** @var array $billGroupLabels */
$model = reset($models);

$form = ActiveForm::begin([
    'id' => 'bill-dynamic-form',
    'action' => $model->isNewRecord ? Url::to(['@bill/create']) : Url::to(['@bill/update', 'id' => $model->id]),
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute([
        'validate-bill-form',
        'scenario' => $model->scenario,
    ]),
]) ?>
<?php DynamicFormWidget::begin([
    'widgetContainer' => 'bills_dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.bill-item', // required: css class
    'limit' => 99, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $model,
    'formId' => 'bill-dynamic-form',
    'formFields' => [
        'class',
        'object_id',
        'client_id',
        'requisite_id',
        'type',
        'sum',
        'time',
        'label',
    ],
]) ?>

<div class="container-items">
    <?php $i = 0; ?>
    <?php foreach ($models as $model) : ?>
        <div class="bill-item">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">&nbsp;</h3>
                    <div class="box-tools">
                        <?php if ($model->isNewRecord) : ?>
                            <div class="btn-group">
                                <button type="button" class="add-item btn btn-box-tool">
                                    <i class="glyphicon glyphicon-plus text-success"></i>
                                </button>
                                <button type="button" class="remove-item btn btn-box-tool">
                                    <i class="glyphicon glyphicon-minus text-danger"></i>
                                </button>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="box-body">

                    <div class="row input-row margin-bottom">
                        <div class="col-lg-offset-10 col-sm-2 text-right">
                            <?= Html::activeHiddenInput($model, "[$i]id") ?>
                        </div>
                        <div class="form-instance">
                            <div class="col-md-3">
                                <?= $form->field($model, "[$i]object_id")->widget(ObjectCombo::class, [
                                    'class_attribute_name' => "[$i]class",
                                ]) ?>
                                <?php if (Yii::$app->user->can('requisites.read')) : ?>
                                    <?= $form->field($model, "[$i]requisite_id")->widget(RequisitesCombo::class) ?>
                                <?php endif ?>
                            </div>
                            <div class="col-md-1">
                                <?= $form->field($model, "[$i]client_id")->widget(ClientCombo::class, [
                                    'formElementSelector' => '.form-instance',
                                    'inputOptions' => [
                                        'readonly' => $model->scenario === Bill::SCENARIO_UPDATE,
                                    ],
                                ]) ?>
                            </div>
                            <div class="col-md-1">
                                <?= $form->field($model, "[$i]type")->dropDownList($billTypes, [
                                    'groups' => $billGroupLabels,
                                    'value' => $model->gtype ? implode(',', [$model->gtype, $model->type]) : $model->type,
                                ]) ?>
                            </div>
                            <div class="col-md-2 <?= AmountWithCurrency::$widgetClass ?>">
                                <?= $form->field($model, "[$i]sum")->widget(AmountWithCurrency::class, [
                                    'currencyAttributeName' => "[$i]currency",
                                    'currencyAttributeOptions' => [
                                        'items' => $this->context->getCurrencyTypes(),
                                    ],
                                    'options' => [
                                        'data-bill-sum' => true,
                                    ],
                                ]) ?>
                                <?= $form->field($model, "[$i]currency", ['template' => '{input}{error}'])->hiddenInput() ?>
                            </div>
                            <div class="col-md-1">
                                <?= Html::activeHiddenInput($model, "[$i]unit") ?>
                                <?= $form->field($model, "[$i]quantity")->input('text', ['value' => $model->getQuantity()]) ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($model, "[$i]time")->widget(DateTimePicker::class, [
                                    'model' => $model,
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd hh:ii:ss',
                                    ],
                                    'options' => [
                                        'value' => Yii::$app->formatter->asDatetime(($model->isNewRecord && empty($model->time) ? new DateTime() : $model->time),
                                            'php:Y-m-d H:i:s'),
                                    ],
                                ]) ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($model, "[$i]label") ?>
                            </div>
                        </div>
                    </div>

                    <?php $charges = $model->getCharges(); ?>
                    <?php $charge = reset($charges); ?>
                    <?php if ($charge) : ?>
                        <div class="row input-row">
                            <?php DynamicFormWidget::begin([
                                'widgetContainer' => 'charges_dynamicform_wrapper',
                                'widgetBody' => '.bill-charges', // required: css class selector
                                'widgetItem' => '.charge-item', // required: css class
                                'limit' => 99, // the maximum times, an element can be cloned (default 999)
                                'min' => 0,
                                'insertButton' => '.add-charge',
                                'deleteButton' => '.remove-charge',
                                'model' => $charge,
                                'formId' => 'bill-dynamic-form',
                                'formFields' => [
                                    'id',
                                    'class',
                                    'object_id',
                                    'type',
                                    'sum',
                                    'unit',
                                    'quantity',
                                    'label',
                                ],
                            ]) ?>
                            <div class="bill-charges">
                                <div class="col-md-12 margin-bottom">
                                    <button type="button" class="add-charge btn btn-sm bg-olive btn-flat">
                                        <i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;<?= Yii::t('hipanel:finance', 'Detalization') ?>
                                    </button>
                                </div>
                                <?php foreach ($charges as $j => $charge) : ?>
                                    <div class="charge-item col-md-12">
                                        <?php if (!$charge->isNewRecord) echo Html::activeHiddenInput($charge, "[$i][$j]id") ?>
                                        <div class="row input-row margin-bottom">
                                            <div class="form-instance">
                                                <div class="col-md-3">
                                                    <?= $form->field($charge, "[$i][$j]object_id")->widget(ObjectCombo::class, [
                                                        'class_attribute_name' => "[$i][$j]class",
                                                        'selectedAttributeName' => 'name',
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <?= $form->field($charge, "[$i][$j]type")->dropDownList($billTypes, [
                                                        'groups' => $billGroupLabels,
                                                        'value' => $charge->ftype,
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <?= $form->field($charge, "[$i][$j]sum")->textInput([
                                                        'data-attribute' => 'sum',
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <?= Html::activeHiddenInput($charge, "[$i][$j]unit") ?>
                                                    <?= $form->field($charge, "[$i][$j]quantity")->input('text', ['value' => $charge->getQuantity()]) ?>
                                                </div>
                                                <div class="col-md-5">
                                                    <?= $form->field($charge, "[$i][$j]label") ?>
                                                </div>
                                            </div>

                                            <div class="col-md-1" style="padding-top: 25px;">
                                                <label>&nbsp;</label>
                                                <button type="button"
                                                        class="remove-charge btn bg-maroon btn-sm btn-flat">
                                                    <i class="glyphicon glyphicon-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <?php DynamicFormWidget::end() ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php $i++ ?>
    <?php endforeach ?>
</div>

<?php $this->registerJs(<<<JS
    $('#dynamic-form').on('change', '.charge-item input[data-attribute=sum]', function () {
        $(this).closest('.bill-item').find('input[data-bill-sum]').blur();
    });
JS
) ?>
<?php DynamicFormWidget::end() ?>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'),
            ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
