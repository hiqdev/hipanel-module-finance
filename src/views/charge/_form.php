<?php

use hipanel\modules\finance\cart\AbstractPurchase;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\widgets\BillTypeVueTreeSelect;
use hipanel\modules\finance\widgets\DateTimePickerWithFormatter;
use hipanel\modules\finance\widgets\PricePerUnitWidget;
use hipanel\modules\finance\widgets\TreeSelectBehavior;
use hipanel\widgets\combo\ObjectCombo;
use hipanel\widgets\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var Charge[] $charges
 * @var Bill $model
 * @var ActiveForm $form
 * @var AbstractPurchase[] $items
 * @var ActiveForm $form
 * @var int $i
 * @var int $j
 * @var array $billTypesList
 * @var array $allowedTypes
 */

?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'charges_dynamicform_wrapper',
    'widgetBody' => '.bill-charges',
    'widgetItem' => '.charge-item',
    'limit' => 999,
    'min' => 0,
    'insertButton' => '.add-charge',
    'deleteButton' => '.remove-charge',
    'model' => reset($charges),
    'formId' => 'bill-dynamic-form',
    'formFields' => [
        'id',
        'class',
        'object_id',
        'type_id',
        'sum',
        'unit',
        'quantity',
        'time',
        'label',
    ],
]) ?>

<div class="bill-charges">

    <div class="col-md-12 margin-bottom">
        <button type="button" class="add-charge btn btn-sm bg-olive btn-flat">
            <i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;<?= yii::t('hipanel:finance', 'detalization') ?>
        </button>
    </div>

<?php foreach ($charges as $j => $charge) : $j++; ?>

    <div class="charge-item col-md-12">
        <?= Html::activeHiddenInput($charge, "[$i][$j]bill_id", ['value' => $model->id]) ?>
        <?= Html::activeHiddenInput($charge, "[$i][$j]currency", ['value' => $model->currency]) ?>
        <?= Html::activeHiddenInput($charge, "[$i][$j]client_id", ['value' => $model->client_id]) ?>
        <?= Html::activeHiddenInput($charge, "[$i][$j]action_id", ['value' => $charge->action_id]) ?>
        <?= Html::activeHiddenInput($charge, "[$i][$j]parent_id", ['value' => $charge->parent_id]) ?>
        <?= Html::activeHiddenInput($charge, "[$i][$j]unit", ['value' => $charge->unit]) ?>
        <?php if (!$charge->isNewRecord && !$model->isNewRecord): ?>
            <?= Html::activeHiddenInput($charge, "[$i][$j]id") ?>
        <?php endif ?>
        <div class="row input-row margin-bottom">
            <div class="form-instance">
                <div class="col-md-3">
                    <?= $form->field($charge, "[$i][$j]object_id")->widget(ObjectCombo::class, [
                        'class_attribute_name' => "[$i][$j]class",
                        'selectedAttributeName' => 'name',
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($charge, "[$i][$j]type_id")->widget(BillTypeVueTreeSelect::class, [
                        'billTypes' => $billTypesList,
                        'replaceAttribute' => 'type_id',
                        'deprecatedTypes' => Yii::$app->params['module.finance.bill.types']['deprecated.types'],
                        'behavior' => $model->isNewRecord ? TreeSelectBehavior::Hidden : TreeSelectBehavior::Disabled,
                        'allowedTypes' => $allowedTypes,
                    ]) ?>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-4">
                            <?= Html::activeHiddenInput($charge, "[$i][$j]unit") ?>
                            <?= $form->field($charge, "[$i][$j]quantity")->input('text', ['value' => $charge->getQuantity()]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= PricePerUnitWidget::widget([
                                'sum' => $charge->sum ?? null,
                                'quantity' => $charge->quantity ?? null,
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($charge, "[$i][$j]sum")->input('text', [
                                'data-attribute' => 'sum',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-1" style="padding-top: 25px;">
                    <label>&nbsp;</label>
                    <button type="button" title="<?= Yii::t('hipanel:finance', 'Delete charge') ?>"
                            class="remove-charge btn bg-maroon btn-sm btn-flat">
                        <i class="glyphicon glyphicon-minus"></i>
                    </button>
                    <button type="button" title="<?= Yii::t('hipanel:finance', 'Repeat charge') ?>"
                            class="add-charge repeat-charge btn btn-warning btn-sm btn-flat">
                        <i class="fa fa-repeat fa-fw"></i>
                    </button>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8">
                            <?= $form->field($charge, "[$i][$j]label") ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($charge, "[$i][$j]time")->widget(DateTimePickerWithFormatter::class) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endforeach ?>

</div>

<?php DynamicFormWidget::end() ?>
