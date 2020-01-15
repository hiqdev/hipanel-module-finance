<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\FormulaInput;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\widgets\AmountWithCurrency;
use hipanel\widgets\XEditable;
use yii\bootstrap\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var Plan|null $plan
 * @var Price $model
 * @var ActiveForm $form
 */
?>

<div class="form-instance">
    <div class="col-md-2" style="white-space: normal">
        <?= Html::activeHiddenInput($model, "[$i]object_id", ['ref' => 'object_id']) ?>
        <?= Html::activeHiddenInput($model, "[$i]plan_id") ?>
        <?= Html::activeHiddenInput($model, "[$i]type") ?>
        <?= Html::activeHiddenInput($model, "[$i]class") ?>
        <?= Html::activeHiddenInput($model, "[$i]object", ['value' => $model->object->name]) ?>
        <?= Html::activeHiddenInput($model, "[$i]quantity") ?>
        <?= Html::activeHiddenInput($model, "[$i]unit") ?>
        <?= Html::activeHiddenInput($model, "[$i]note", ['data-attribute' => 'note']) ?>

        <div class="form-group">
            <?php if ($model->object->name === null): ?>
                <i><?= Yii::t('hipanel.finance.price', 'Any object') ?></i>
            <?php else: ?>
                <strong>
                    <?= LinkToObjectResolver::widget([
                        'model' => $model->object,
                        'labelAttribute' => 'name',
                        'linkOptions' => [
                            'tabindex' => '-1',
                        ],
                    ]) ?>
                </strong>
            <?php endif ?>
            <?php if (!empty($model->object->label)) : ?>
                <br/><?= Html::encode($model->object->label) ?>
            <?php endif; ?>
            <br/>
            <?= BillType::widget([
                'model' => $model,
                'field' => 'type',
            ]) ?>
            <br/>
            <?= XEditable::widget([
                'model' => $model,
                'attribute' => 'note',
                'pluginOptions' => [
                    'url' => new JsExpression(<<<'JS'
                    function(params) {
                        $(this).closest('.form-instance').find('input[data-attribute=note]').val(params.value);
                        return $.Deferred().resolve();
                    }
JS
                    ),
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
                'currencyAttributeName' => 'currency',
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
    <?php if ($model->hasAttribute('count_aggregated_traffic')) : ?>
        <div class="col-md-12 col-md-offset-2" style="margin-top: -1em">
            <?= $form->field($model, "[$i]count_aggregated_traffic")->checkbox()->hint(Yii::t('hipanel.finance.price', '(reasonable for grouping tariffs only)')) ?>
        </div>
    <?php endif ?>
</div>
