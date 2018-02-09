<?php

use hipanel\widgets\AmountWithCurrency;
use yii\bootstrap\Html;

/**
 * @var \hipanel\modules\finance\models\Plan|null $plan
 * @var \hipanel\modules\finance\models\Price $price
 * @var \yii\widgets\ActiveForm $form
 */
?>

<div class="form-instance">
    <div class="col-md-3">
        <?= Html::activeHiddenInput($model, "[$i]object_id", ['ref' => 'object_id']) ?>

        <div class="form-group">
            <label class="control-label"><?= Yii::t('hipanel', 'Object') ?></label>
            <div>
                <?= \hipanel\modules\finance\widgets\LinkToObjectResolver::widget([
                    'model' => $model->object,
                    'labelAttribute' => 'name',
                ]) ?>
            </div>
        </div>
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
            ]) ?>
            <?= $form->field($model, "[$i]currency", ['template' => '{input}{error}'])->hiddenInput([
                'ref' => 'currency',
            ]) ?>
        </div>
    </div>
    <div class="col-md-1">
        <?= $form->field($model, "[$i]unit")->dropDownList($model->unitOptions, ['prompt' => '--']) ?>
    </div>
    <div class="col-md-1">
        <?= $form->field($model, "[$i]quantity") ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, "[$i]note") ?>
    </div>
    <div class="col-md-1" style="padding-top: 25px;">
        <label>&nbsp;</label>
        <button type="button" class="remove-item btn bg-maroon btn-sm btn-flat" tabindex="-1">
            <i class="glyphicon glyphicon-minus"></i>
        </button>
    </div>
</div>
