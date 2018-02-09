<?php

use hipanel\widgets\AmountWithCurrency;
use yii\bootstrap\Html;

/**
 * @var \hipanel\modules\finance\models\Plan|null $plan
 * @var \hipanel\modules\finance\models\ModelGroupPrice $model
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
        <?= $form->field($model, "[$i]object")->textInput([
            'disabled' => true,
            'ref' => 'object',
            'value' => $model->object->name,
        ])->label(Yii::t('hipanel', 'Group')) ?>
    </div>
    <div class="col-md-2">
        <div class="<?= AmountWithCurrency::$widgetClass ?>">
            <?= $form->field($model, "[$i]price")->widget(AmountWithCurrency::class, [
                'currencyAttributeName' => "currency",
                'currencyAttributeOptions' => [
                    'items' => $this->context->getCurrencyTypes(),
                ],
                'currencyDropdownOptions' => [
                    'disabled' => true,
                ],
            ]) ?>
        </div>
    </div>
    <?php foreach ($model->subprices as $currCode => $subprice): ?>
        <div class="col-md-2">
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
                    ],
                    'currencyAttributeOptions' => [
                        'items' => $this->context->getCurrencyTypes(),
                    ],
                ])->label(Yii::t('hipanel.finance.price', 'Price in {currency}', ['currency' => $currCode])) ?>
            </div>
        </div>
    <?php endforeach; ?>
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
