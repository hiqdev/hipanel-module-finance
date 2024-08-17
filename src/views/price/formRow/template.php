<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\TemplatePrice;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\modules\finance\widgets\PriceFields;
use hipanel\widgets\AmountWithCurrency;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/**
 * @var Plan|null $plan
 * @var TemplatePrice $model
 * @var ActiveForm $form
 * @var int $i
 * @var array $currencyTypes
 */
?>

<div class="form-instance">
    <div class="col-md-2" style="white-space: normal">
        <?= Html::activeHiddenInput($model, "[$i]object_id", ['ref' => 'object_id']) ?>
        <?= Html::activeHiddenInput($model, "[$i]type") ?>
        <?= Html::activeHiddenInput($model, "[$i]class") ?>
        <?= Html::activeHiddenInput($model, "[$i]object", ['value' => $model->object->name ?? '']) ?>
        <?= Html::activeHiddenInput($model, "[$i]note") ?>
        <strong>
            <?= LinkToObjectResolver::widget([
                'model' => $model->object,
                'labelAttribute' => 'name',
            ]) ?>
        </strong>
        <br/>
        <?= BillType::widget([
            'model' => $model,
            'field' => 'type',
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= PriceFields::widget(['model' => $model, 'form' => $form, 'index' => $i, 'currencyTypes' => $this->context->getCurrencyTypes()]) ?>
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
