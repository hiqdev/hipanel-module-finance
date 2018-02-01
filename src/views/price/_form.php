<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Price;
use hipanel\widgets\AmountWithCurrency;
use hipanel\widgets\Box;
use hipanel\widgets\DynamicFormWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \hipanel\modules\finance\models\Price[] $models */
/** @var \hipanel\modules\finance\models\Plan|null $plan */

$model = reset($models);

$form = ActiveForm::begin([
    'id' => 'plan-form',
    'action' => $model->isNewRecord ? Url::to(['@price/create-suggested']) : Url::to(['@price/update', 'id' => $model->id]),
    'enableClientValidation' => true,
    'validationUrl' => Url::toRoute([
        'validate-form',
        'scenario' => $model->isNewRecord ? $model->scenario : Price::SCENARIO_UPDATE,
    ]),
]) ?>
<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.price-item', // required: css class
    'limit' => 99, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $model,
    'formId' => 'plan-form',
    'formFields' => [
        'id', 'quantity', 'type', 'unit', 'price', 'currency', 'note'
    ],
]) ?>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= $plan ? Yii::t('hipanel.finance.price', 'Tariff: {name}', ['name' => $plan->name]) : '' ?>
        </h3>
    </div>
    <div class="container-items">
        <?php $i = 0; ?>
        <?php foreach ($models as $model) : ?>
            <div class="box-body price-item">
                <div class="row input-row">
                    <div class="col-lg-offset-10 col-sm-2 text-right">
                        <?= Html::activeHiddenInput($model, "[$i]id") ?>
                        <?php if ($plan): ?>
                            <?php $model->plan_id = $plan->id ?>
                        <?php endif ?>
                        <?= Html::activeHiddenInput($model, "[$i]plan_id", ['ref' => 'plan_id']) ?>
                    </div>
                    <div class="form-instance">
                        <div class="col-md-2">
                            <?= Html::activeHiddenInput($model, "[$i]object_id", ['ref' => 'object_id']) ?>
                            <?= $form->field($model, "[$i]object")->textInput([
                                'disabled' => true,
                                'ref' => 'object',
                                'value' => $model->object->name,
                            ])->label(Yii::t('hipanel', 'Object')) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, "[$i]type")->dropDownList($model->typeOptions, ['prompt' => '--']) ?>
                        </div>
                        <div class="col-md-2">
                            <div class="<?= AmountWithCurrency::$widgetClass ?>">
                                <?= $form->field($model, "[$i]price")->widget(AmountWithCurrency::class, [
                                    'currencyAttributeName' => "currency",
                                    'currencyAttributeOptions' => [
                                        'items' => $this->context->getCurrencyTypes(),
                                    ],
                                    'inputOptions' => [
                                        'data-bill-sum' => true,
                                    ],
                                ]) ?>
                                <?= $form->field($model, "[$i]currency", ['template' => '{input}{error}'])->hiddenInput([
                                    'ref' => 'currency'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, "[$i]unit")->dropDownList($model->unitOptions, ['prompt' => '--']) ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, "[$i]quantity") ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, "[$i]note") ?>
                        </div>
                        <div class="col-md-1" style="padding-top: 25px;">
                            <label>&nbsp;</label>
                            <button type="button" class="remove-item btn bg-maroon btn-sm btn-flat">
                                <i class="glyphicon glyphicon-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php $i++ ?>
        <?php endforeach ?>
    </div>
</div>
<?php DynamicFormWidget::end() ?>
<?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'),
            ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>
<?php Box::end() ?>
<?php ActiveForm::end() ?>

<?= \hipanel\widgets\DynamicFormInputsValueInheritor::widget([
    'itemSelector' => '.price-item',
    'inputSelectors' => ['input[ref=object_id]', 'input[ref=object]', 'input[ref=plan_id]', 'input[ref=currency]']
]) ?>
