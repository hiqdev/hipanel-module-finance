<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\widgets\combo\BillRequisitesCombo;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var \hipanel\modules\finance\forms\GenerateInvoiceForm $model */

?>

<?php $form = ActiveForm::begin([
    'id' => 'generate-invoice-form',
    'action' => Url::to(['@bill/generate-invoice']),
    'enableClientValidation' => true,
    'validateOnBlur' => false,
    'validateOnChange' => false,
    'enableAjaxValidation' => false,
]) ?>

<?= Html::activeHiddenInput($model, 'purse_id') ?>

<?= $form->field($model, 'requisite_id')->widget(BillRequisitesCombo::class) ?>


<?= Html::submitButton() ?>

<?php ActiveForm::end() ?>
