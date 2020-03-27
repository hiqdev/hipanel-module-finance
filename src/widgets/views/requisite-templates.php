<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use hipanel\widgets\RefCombo;
use yii\web\View;

/**
 * @var View
 * @var array|string $actionUrl url to send the form
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'nss-form-pjax',
    'action' => $actionUrl,
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'validationUrl' => Url::toRoute(['@requisite/validate-form', 'scenario' => 'set-templates']),
]) ?>

<?php if (!is_array($model)) : ?>
    <?= Html::activeHiddenInput($model, 'id') ?>
<?php else : ?>
    <?php foreach ($model as $item) : ?>
        <?= Html::activeHiddenInput($item, "[$item->id]id") ?>
    <?php endforeach ?>
<?php endif ?>

<div class="row" style="padding: 1em 5px 0;">
    <?php $model = is_array($model) ? reset($model) : $model ?>
    <?php foreach (['invoice', 'acceptance', 'contract', 'probation'] as $reqtemplate) : ?>
        <div class="col-md-6">
            <?= $form->field($model, "{$reqtemplate}_id")->widget(RefCombo::class, [
                'gtype' => "type,document,{$reqtemplate}",
            ]) ?>
        </div>
    <?php endforeach ?>
</div>

<?= Html::submitButton(Yii::t('hipanel', 'Save'), [
    'class' => 'btn btn-success',
    'id' => 'nss-save-button',
    'data-loading-text' => '<i class="fa fa-circle-o-notch fa-spin"></i> ' . Yii::t('hipanel', 'saving'),
    'disabled' => false,
]) ?>
<?php ActiveForm::end() ?>
