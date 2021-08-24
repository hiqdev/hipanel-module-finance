<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\widgets\PricePerUnitWidget;
use hipanel\modules\finance\widgets\combo\MultipleBillTypeCombo;
use hipanel\modules\finance\widgets\combo\BillRequisitesCombo;
use hipanel\modules\finance\widgets\SumSignToggleButton;
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

$this->registerCss("
#bill-dynamic-form .charge-item .col-md-1,
#bill-dynamic-form .charge-item .col-md-2,
#bill-dynamic-form .charge-item .col-md-3,
#bill-dynamic-form .charge-item .col-md-4,
#bill-dynamic-form .charge-item .col-md-5,
#bill-dynamic-form .charge-item .col-md-6,
#bill-dynamic-form .charge-item .col-md-7,
#bill-dynamic-form .charge-item .col-md-8,
#bill-dynamic-form .charge-item .col-md-9,
#bill-dynamic-form .charge-item .col-md-10,
#bill-dynamic-form .charge-item .col-md-11,
#bill-dynamic-form .charge-item .col-md-12 {
  padding-right: 10px;
  padding-left: 10px;
}
#bill-dynamic-form .charge-item .row {
  margin-right: -10px;
  margin-left: -10px;
}
}
");

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
                    <div class="box-title">
                        <?= SumSignToggleButton::widget() ?>
                    </div>
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

                    <div class="row input-row">
                        <?= Html::activeHiddenInput($model, "[$i]id") ?>
                        <div class="form-instance">
                            <div class="col-md-3">
                                <?= $form->field($model, "[$i]object_id")->widget(ObjectCombo::class, [
                                    'class_attribute_name' => "[$i]class",
                                ]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, "[$i]client_id")->widget(ClientCombo::class, [
                                    'filter' => ['state' => ['format' => 'ok']],
                                    'formElementSelector' => '.form-instance',
                                    'inputOptions' => [
                                        'readonly' => $model->scenario === Bill::SCENARIO_UPDATE,
                                    ],
                                    'current' => [
                                        $model->client_id => $model->client
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, "[$i]type")->widget(MultipleBillTypeCombo::class, [
                                    'billTypes' => $billTypes,
                                    'billGroupLabels' => $billGroupLabels,
                                    'multiple' => false,
                                    'useFullType' => true,
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
                        </div>
                    </div>
                    <div class="row input-row margin-bottom">
                        <div class="col-md-3">
                            <?php if (Yii::$app->user->can('requisites.read')) : ?>
                                <?= $form->field($model, "[$i]requisite_id")->widget(BillRequisitesCombo::class) ?>
                            <?php endif ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, "[$i]label") ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, "[$i]txn") ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, "[$i]time")->widget(DateTimePicker::class, [
                                'model' => $model,
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd hh:ii:ss',
                                ],
                                'options' => [
                                    'value' => isset($model->time)
                                        ? ($model->time !== false ? Yii::$app->formatter->asDatetime($model->time, 'php:Y-m-d H:i:s') : null)
                                        : Yii::$app->formatter->asDatetime(new DateTime(), 'php:Y-m-d H:i:s'),
                                ],
                            ]) ?>
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
                                        <?php if (!$charge->isNewRecord && !$model->isNewRecord): ?>
                                            <?= Html::activeHiddenInput($charge, "[$i][$j]id") ?>
                                        <?php endif ?>
                                        <div class="row input-row margin-bottom">
                                            <div class="form-instance">
                                                <div class="col-md-2">
                                                    <?= $form->field($charge, "[$i][$j]object_id")->widget(ObjectCombo::class, [
                                                        'class_attribute_name' => "[$i][$j]class",
                                                        'selectedAttributeName' => 'name',
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <?= $form->field($charge, "[$i][$j]type")->dropDownList($billTypes, [
                                                        'groups' => $billGroupLabels,
                                                        'value' => $charge->ftype ?? $charge->type,
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <?= Html::activeHiddenInput($charge, "[$i][$j]unit") ?>
                                                            <?= $form->field($charge, "[$i][$j]quantity")->input('text', ['value' => $charge->getQuantity()]) ?>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <?= PricePerUnitWidget::widget([
                                                                'sum' => $charge->sum ?? null,
                                                                'quantity' => $charge->quantity ?? null,
                                                            ]) ?>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <?= $form->field($charge, "[$i][$j]sum")->input('text', [
                                                                'data-attribute' => 'sum',
                                                            ]) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <?= $form->field($charge, "[$i][$j]label") ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <?= $form->field($charge, "[$i][$j]time")->widget(DateTimePicker::class, [
                                                        'clientOptions' => [
                                                            'format' => 'yyyy-mm-dd hh:ii:ss',
                                                            'autoclose' => true,
                                                            'clearBtn' => true,
                                                            'minView' => 2,
                                                        ],
                                                        'options' => [
                                                            'placeholder' => Yii::t('hipanel', 'Select date'),
                                                        ],
                                                    ]) ?>
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
    $('#bill-dynamic-form').on('change', '.bill-charges .charge-item input[data-attribute=sum]', function () {
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
