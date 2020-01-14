<?php

use hipanel\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use hipanel\widgets\RefCombo;

/**
 * @var \yii\web\View
 * @var array|string $actionUrl url to send the form
 */
?>

<?php Pjax::begin(['id' => 'nss-pjax-container', 'enablePushState' => false, 'enableReplaceState' => false]) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'nss-form-pjax',
        'action' => $actionUrl,
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'validationUrl' => Url::toRoute(['@requisite/validate-templates', 'scenario' => 'default']),
        'options' => [
            'data-pjax' => true,
            'data-pjaxPush' => false,
        ],
    ]) ?>
        <?php if (!is_array($model)) : ?>
            <?php if ($model->isSetNSable()) : ?>
                <?= Html::activeHiddenInput($model, 'id') ?>
            <?php endif ?>
        <?php else : ?>
            <?php foreach ($model as $item) : ?>
                <?= Html::activeHiddenInput($item, "[$item->id]id") ?>
            <?php endforeach ?>
        <?php endif ?>

        <div class="row" style="margin-top: 15pt;">
            <div class="col-md-10 inline-form-selector">
                <?php $model = is_array($model) ? reset($model) : $model ?>
                <?php foreach (['invoice', 'acceptance', 'contract', 'probation'] as $reqtemplate) : ?>
                    <div class="col-md-6">
                        <?= $form->field($model, "{$reqtemplate}_id")->widget(RefCombo::class, [
                            'gtype' => "type,document,{$reqtemplate}",
                        ]) ?>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="col-md-2 text-right">
                <?= Html::submitButton(Yii::t('hipanel', 'Save'), [
                    'class' => 'btn btn-success',
                    'id' => 'nss-save-button',
                    'data-loading-text' => '<i class="fa fa-circle-o-notch fa-spin"></i> ' . Yii::t('hipanel', 'saving'),
                    'disabled' => false,
                ]) ?>
            </div>
        </div>
    <?php ActiveForm::end() ?>
<?php Pjax::end() ?>
