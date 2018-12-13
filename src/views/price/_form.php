<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\assets\PriceEstimator;
use hipanel\modules\finance\models\Price;
use hipanel\widgets\Box;
use hipanel\widgets\DynamicFormWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \hipanel\modules\finance\models\Price[] $models */
/** @var \hipanel\modules\finance\models\Plan|null $plan */

$model = reset($models);

$form = ActiveForm::begin([
    'id' => 'prices-form',
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
    'formId' => 'prices-form',
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
            <div class="box-body price-item" data-id="<?= $model->id ?? uniqid() ?>">
                <?= Html::activeHiddenInput($model, "[$i]id") ?>
                <?php if ($plan): ?>
                    <?php $model->plan_id = $plan->id ?>
                <?php endif ?>
                <?= Html::activeHiddenInput($model, "[$i]plan_id", ['ref' => 'plan_id']) ?>
                <div class="row input-row">
                    <?php if ($model instanceof \hipanel\modules\finance\models\TemplatePrice): ?>
                        <?= $this->render('formRow/template', compact('plan', 'model', 'form', 'i')) ?>
                    <?php else: ?>
                        <?= $this->render('formRow/simple', compact('plan', 'model', 'form', 'i')) ?>
                    <?php endif ?>
                </div>
            </div>
            <?php $i++ ?>
        <?php endforeach ?>

        <div class="box-footer with-border">
                <?= Html::tag('p', Yii::t('hipanel:finance', 'Total:'), ['class' => 'total-block', 'id' => 'total-label']) ?>
                <?= Html::tag('p', Html::encode(''), ['class' => 'total-block', 'id' => 'total-value']) ?>
        </div>

    </div>
</div>

<?php DynamicFormWidget::end() ?>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel:finance', 'Update estimates'), ['class' => 'btn btn-info', 'id' => 'update-estimates']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>
<?php ActiveForm::end() ?>

<?= \hipanel\widgets\DynamicFormInputsValueInheritor::widget([
    'itemSelector' => '.price-item',
    'inputSelectors' => ['input[ref=object_id]', 'input[ref=object]', 'input[ref=plan_id]', 'input[ref=currency]']
]) ?>

<?php
PriceEstimator::register($this);
$this->registerJs(<<<'JS'
$('#prices-form').priceEstimator({
    rowSelector: '.price-item',
    totalCellSelector: '#total-value' 
});

$('#update-estimates').click(function() {
    $('#total-label').css({display: 'inline-block'});
    $('#prices-form').priceEstimator().update();
})

hipanel.form.preventSubmitWithEnter('#prices-form')
JS
);

$this->registerCss(<<<'CSS'
#total-value {
    font-size: 110%;
    margin-left: 5px;
}

#total-value i {
    font-size: 90%;
}

#total-label {
    font-size: 120%;
    text-transform: uppercase;
    font-weight: bold;

    display: none;
}

.total-block {
    display: inline-block;
    margin-bottom: 0;
}
CSS
);
