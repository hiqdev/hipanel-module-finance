<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\TemplatePrice;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\widgets\AmountWithCurrency;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use yii\bootstrap\Html;
use yii\helpers\StringHelper;
use yii\widgets\ActiveForm;

/**
 * @var Plan|null $plan
 * @var TemplatePrice $model
 * @var ActiveForm $form
 * @var int $i
 */
?>

<div class="form-instance">
    <div class="col-md-2" style="white-space: normal">
        <?= Html::activeHiddenInput($model, "[$i]object_id", ['ref' => 'object_id']) ?>
        <?= Html::activeHiddenInput($model, "[$i]unit") ?>
        <?= Html::activeHiddenInput($model, "[$i]quantity") ?>
        <?= Html::activeHiddenInput($model, "[$i]type") ?>
        <?= Html::activeHiddenInput($model, "[$i]currency") ?>
        <?= Html::activeHiddenInput($model, "[$i]class") ?>
        <?= Html::activeHiddenInput($model, "[$i]object") ?>
        <strong>
            <?= LinkToObjectResolver::widget([
                'model' => $model->object,
                'labelAttribute' => 'name',
            ]) ?>
        </strong>
        <br />
        <?= BillType::widget([
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
            <?php if (StringHelper::startsWith($model->type, Plan::TYPE_REFERRAL)) : ?>
                <?= $form->field($model, "[$i]rate")->input('number', ['min' => 0]) ?>
                <?= Html::activeHiddenInput($model, "[$i]price") ?>
                <?= Html::activeHiddenInput($model, "[$i]currency") ?>
            <?php else : ?>
                <div class="<?= AmountWithCurrency::$widgetClass ?>">
                    <?= $form->field($model, "[$i]price")->widget(AmountWithCurrency::class, [
                        'currencyAttributeName' => 'currency',
                        'currencyAttributeOptions' => [
                            'items' => $this->context->getCurrencyTypes(),
                        ],
                        'currencyDropdownOptions' => [
                            'disabled' => true,
                            'hidden' => true,
                        ],
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($model->subprices) : ?>
            <?php foreach ($model->subprices as $currCode => $subprice): ?>
                <div class="col-md-4">
                    <div class="<?= AmountWithCurrency::$widgetClass ?>">
                        <?= $form->field($model, "[$i]subprices")->widget(AmountWithCurrency::class, [
                            'options' => [
                                'id' => Html::getInputName($model, "[$i][subprices]$currCode"),
                                'name' => Html::getInputName($model, "[$i][subprices]$currCode"),
                                'value' => Yii::$container->get(DecimalMoneyFormatter::class)
                                                ->format(new Money($model->subprices[$currCode]['amount'] ?? 0, new Currency($currCode))),
                            ],
                            'selectedCurrencyCode' => $currCode,
                            'currencyAttributeName' => 'subprices',
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
        <?php endif; ?>
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
