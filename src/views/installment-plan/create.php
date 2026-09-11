<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\InstallmentPlan;
use hipanel\widgets\DatePicker;
use hiqdev\combo\Combo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var InstallmentPlan $model */
/** @var array $currencyTypes */

$this->title = Yii::t('hipanel:finance', 'Create Installment Plan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Installment plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="installment-plan-create">
    <?php $form = ActiveForm::begin(['id' => 'installment-plan-create-form']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'part_id')->widget(Combo::class, [
                'type' => 'stock/part/id',
                'name' => 'serial',
                'url' => '/stock/part/index',
                'return' => ['id'],
                'rename' => ['text' => 'serial'],
                'primaryFilter' => 'serial_ilike',
            ]) ?>
            <?= $form->field($model, 'client_id')->widget(ClientCombo::class) ?>
            <?= $form->field($model, 'currency')->dropDownList($currencyTypes) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'monthly_sum')->textInput() ?>
            <?= $form->field($model, 'since')->widget(DatePicker::class) ?>
            <?= $form->field($model, 'months')->textInput(['type' => 'number', 'min' => 1]) ?>
            <?= $form->field($model, 'reason')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('hipanel', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
