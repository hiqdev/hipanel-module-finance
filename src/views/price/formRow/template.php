<?php

use hipanel\widgets\AmountWithCurrency;
use yii\bootstrap\Html;

/**
 * @var \hipanel\modules\finance\models\Plan|null $plan
 * @var \hipanel\modules\finance\models\TemplatePrice $model
 * @var \yii\widgets\ActiveForm $form
 * @var int $i
 */
?>

<div class="form-instance">
    <div class="col-md-2">
        <?= Html::activeHiddenInput($model, "[$i]object_id", ['ref' => 'object_id']) ?>
        <?= Html::activeHiddenInput($model, "[$i]unit") ?>
        <?= Html::activeHiddenInput($model, "[$i]quantity") ?>
        <?= Html::activeHiddenInput($model, "[$i]type") ?>
        <?= Html::activeHiddenInput($model, "[$i]currency") ?>
        <?= Html::activeHiddenInput($model, "[$i]class") ?>
        <?= Html::activeHiddenInput($model, "[$i]object") ?>
        <strong>
            <?= \hipanel\modules\finance\widgets\LinkToObjectResolver::widget([
                'model' => $model->object,
                'labelAttribute' => 'name'
            ]) ?>
        </strong>
        <br />
        <?= \hipanel\modules\finance\widgets\PriceType::widget([
            'model' => $model,
            'field' => 'type',
        ])?>
    </div>
    <div class="col-md-1">
        <?php if ($model->isOveruse()): ?>
            <?= $form->field($model, "[$i]quantity")->textInput() ?>
        <?php endif ?>
    </div>
    <div class="col-md-1">
        <?php if ($model->unitOptions !== []): ?>
            <?= $form->field($model, "[$i]unit")->dropDownList($model->unitOptions) ?>
        <?php endif ?>
    </div>
    <div class="col-md-4">
        <div class="col-md-4">
            <div class="<?= AmountWithCurrency::$widgetClass ?>">
                <?= $form->field($model, "[$i]price")->widget(AmountWithCurrency::class, [
                    'currencyAttributeName' => "currency",
                    'currencyAttributeOptions' => [
                        'items' => $this->context->getCurrencyTypes(),
                    ],
                    'currencyDropdownOptions' => [
                        'disabled' => true,
                        'hidden' => true,
                    ],
                ]) ?>
            </div>
        </div>
        <?php foreach ($model->subprices as $currCode => $subprice): ?>
            <div class="col-md-4">
                <div class="<?= AmountWithCurrency::$widgetClass ?>">
                    <?= $form->field($model, "[$i]subprices")->widget(AmountWithCurrency::class, [
                        'inputOptions' => [
                            'id' => Html::getInputName($model, "[$i][subprices]$currCode"),
                            'name' => Html::getInputName($model, "[$i][subprices]$currCode"),
                            'value' => $model->subprices[$currCode] ?? 0
                        ],
                        'selectedCurrencyCode' => $currCode,
                        'currencyAttributeName' => "subprices",
                        'currencyDropdownOptions' => [
                            'disabled' => true,
                            'hidden' => true,
                        ],
                        'currencyAttributeOptions' => [
                            'items' => $this->context->getCurrencyTypes(),
                        ],
                    ])->label(Yii::t('hipanel.finance.price', 'Price in {currency}', ['currency' => $currCode])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, "[$i]note") ?>
    </div>
    <div class="col-md-1" style="padding-top: 25px;">
        <label>&nbsp;</label>
        <button type="button" class="remove-item btn bg-maroon btn-sm btn-flat" tabindex="-1">
            <i class="glyphicon glyphicon-minus"></i>
        </button>
    </div>
</div>
