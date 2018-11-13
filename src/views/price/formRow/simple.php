<?php

use hipanel\modules\finance\widgets\FormulaInput;
use hipanel\widgets\AmountWithCurrency;
use yii\bootstrap\Html;

/**
 * @var \hipanel\modules\finance\models\Plan|null $plan
 * @var \hipanel\modules\finance\models\Price $
 * @var \yii\widgets\ActiveForm $form
 */
?>

<div class="form-instance">
    <div class="col-md-2">
        <?= Html::activeHiddenInput($model, "[$i]object_id", ['ref' => 'object_id']) ?>
        <?= Html::activeHiddenInput($model, "[$i]type") ?>
        <?= Html::activeHiddenInput($model, "[$i]class") ?>
        <?= Html::activeHiddenInput($model, "[$i]object", ['value' => $model->object->name]) ?>
        <?= Html::activeHiddenInput($model, "[$i]quantity") ?>
        <?= Html::activeHiddenInput($model, "[$i]unit") ?>
        <?= Html::activeHiddenInput($model, "[$i]note", ['data-attribute' => 'note']) ?>

        <div class="form-group">
            <strong>
                <?= \hipanel\modules\finance\widgets\LinkToObjectResolver::widget([
                    'model' => $model->object,
                    'labelAttribute' => 'name',
                    'linkOptions' => [
                        'tabindex' => '-1'
                    ]
                ]) ?>
            </strong>
            <br />
            <?= \hipanel\modules\finance\widgets\PriceType::widget([
                'model' => $model,
                'field' => 'type',
            ])?>
            <br />
            <?= \hipanel\widgets\XEditable::widget([
                'model' => $model,
                'attribute' => 'note',
                'pluginOptions' => [
                    'url' => new \yii\web\JsExpression(<<<'JS'
                    function(params) {
                        $(this).closest('.form-instance').find('input[data-attribute=note]').val(params.value);
                        return $.Deferred().resolve();
                    }
JS
)
                ],
            ]) ?>
        </div>
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
        <div class="price-estimates">
        </div>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, "[$i]formula")->widget(FormulaInput::class) ?>
    </div>
    <div class="col-md-1" style="padding-top: 25px;">
        <label>&nbsp;</label>
        <button type="button" class="remove-item btn bg-maroon btn-sm btn-flat" tabindex="-1">
            <i class="glyphicon glyphicon-minus"></i>
        </button>
    </div>
</div>
