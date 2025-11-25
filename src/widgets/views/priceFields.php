<?php declare(strict_types=1);

use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\ProgressivePrice;
use hipanel\modules\finance\widgets\ProgressivePresenter;
use hipanel\widgets\DynamicFormWidget;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use hipanel\widgets\AmountWithCurrency;

/**
 * @var Price|ProgressivePrice $model
 * @var ActiveForm $form
 * @var int $index
 * @var array $currencyTypes
 */

$unitCollection = $model->getUnitCollection();
?>

<div class="row  <?= $model->isProgressive() ? 'well well-sm' : '' ?>">
    <div class="col-md-3">
        <?php if ($unitCollection->isEmpty()): ?>
            <?= Html::activeHiddenInput($model, "[$index]unit") ?>
        <?php else : ?>
            <?= $form->field($model, "[$index]unit")->dropDownList($unitCollection->toArray()) ?>
        <?php endif ?>
    </div>
    <div class="col-md-3">
        <?php if ($model->isOveruse()): ?>
            <?= $form->field($model, "[$index]quantity")->textInput() ?>
        <?php else : ?>
            <?= Html::activeHiddenInput($model, "[$index]quantity") ?>
        <?php endif ?>
    </div>
    <div class="col-md-4">
        <?php if ($model->isRate()) : ?>
            <?= $form->field($model, "[$index]rate")->input('number') ?>
            <?= Html::activeHiddenInput($model, "[$index]price", ['value' => 0]) ?>
            <?= Html::activeHiddenInput($model, "[$index]currency", ['value' => $model->plan->currency]) ?>
            <?= Html::activeHiddenInput($model, "[$index]unit", ['value' => $model->plan->currency]) ?>
        <?php else : ?>
            <div class="<?= AmountWithCurrency::$widgetClass ?>">
                <?= $form->field($model, "[$index]price")->widget(AmountWithCurrency::class, [
                    'currencyAttributeName' => 'currency',
                    'currencyAttributeOptions' => [
                        'items' => $currencyTypes,
                    ],
                ]) ?>
                <?= $form->field($model, "[$index]currency", ['template' => '{input}{error}'])->hiddenInput([
                    'ref' => 'currency',
                ]) ?>
            </div>
            <div class="price-estimates"></div>
        <?php endif ?>
    </div>
    <?php if ($model->isProgressive()) : ?>
        <?php $thresholds = $model->getThresholds() ?>
        <?php $dynamicFormWdget = DynamicFormWidget::begin([
            'widgetContainer' => 'thresholds_dynamic_form_wrapper',
            'widgetBody' => '.price-thresholds',
            'widgetItem' => '.threshold-item',
            'limit' => 21,
            'min' => 0,
            'insertButton' => '.add-threshold',
            'deleteButton' => '.remove-threshold',
            'model' => reset($thresholds),
            'formId' => 'prices-form',
            'formFields' => [
                'quantity',
                'price',
            ],
        ]) ?>
        <div class="price-thresholds">
            <div class="col-md-10 text-right" style="position: relative;">
                <button type="button" class="btn btn-success btn-xs add-threshold"
                        title="<?= Yii::t('hipanel:finance', 'Make progressive') ?>" data-testid="add progression">
                    <i class="fa fa-plus fa-fw"></i>
                    <?= Yii::t('hipanel:finance', 'Add progression') ?>
                </button>
                <?= ProgressivePresenter::widget([
                    'price' => $model,
                    'index' => $index,
                    'dynamicFormWidgetContainerClass' => $dynamicFormWdget->widgetContainer,
                ]) ?>
            </div>
            <?php foreach ($thresholds as $j => $threshold) : ?>
                <div class="threshold-item col-md-12">
                    <div class="row" style="margin-top: 1em;">
                        <div class="col-md-offset-3 col-md-3">
                            <?= $form->field($threshold, "[$index][$j]quantity")->input('number',
                                ['placeholder' => $threshold->getAttributeLabel('quantity')])->label(false) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($threshold, "[$index][$j]price")->input('number',
                                ['placeholder' => $threshold->getAttributeLabel('price'), 'step' => 0.0001])->label(false) ?>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm remove-threshold"
                                    title="<?= Yii::t('hipanel:finance', 'Remove progression') ?>">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <?php DynamicFormWidget::end() ?>
    <?php endif ?>
</div>
